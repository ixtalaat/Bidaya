<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Enrollment;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateEnrollmentStatusAction
{
    public function __construct(private AuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws ValidationException
     */
    public function handle(Enrollment $enrollment, array $validated): Enrollment
    {
        $newStatus = EnrollmentStatus::from($validated['status']);

        if ($enrollment->status !== $newStatus && ! in_array($newStatus, $enrollment->status->allowedTransitions(), true)) {
            throw ValidationException::withMessages([
                'status' => __('enrollments.invalid_transition'),
            ]);
        }

        return DB::transaction(function () use ($enrollment, $validated, $newStatus): Enrollment {
            $previousStatus = $enrollment->status;

            $enrollment->update([
                'status' => $newStatus,
                'end_date' => $validated['end_date'] ?? $enrollment->end_date,
            ]);

            if ($newStatus === EnrollmentStatus::Cancelled) {
                $this->settleBillingOnCancel($enrollment);
            }

            $this->audit->log(
                auth()->user(),
                'enrollment.status_changed',
                $enrollment,
                ['status' => $previousStatus->value],
                ['status' => $newStatus->value]
            );

            return $enrollment->refresh();
        });
    }

    /**
     * Move open invoices to a covered sibling instead of voiding them.
     */
    private function settleBillingOnCancel(Enrollment $enrollment): void
    {
        $orphans = Enrollment::where('student_id', $enrollment->student_id)
            ->active()
            ->where('id', '!=', $enrollment->id)
            ->where('covered_by_enrollment_id', $enrollment->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($orphans->isEmpty()) {
            $enrollment->invoices()
                ->where('status', InvoiceStatus::Pending)
                ->update(['status' => InvoiceStatus::Cancelled]);

            return;
        }

        $successor = $orphans->shift();
        $successor->update(['covered_by_enrollment_id' => null]);

        $enrollment->invoices()
            ->whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::PartiallyPaid])
            ->update(['enrollment_id' => $successor->id]);

        foreach ($orphans as $orphan) {
            $orphan->update(['covered_by_enrollment_id' => $successor->id]);
        }

        $this->audit->log(
            auth()->user(),
            'enrollment.billing_transferred',
            $successor,
            ['covered_by_enrollment_id' => $enrollment->id],
            ['covered_by_enrollment_id' => null]
        );
    }
}
