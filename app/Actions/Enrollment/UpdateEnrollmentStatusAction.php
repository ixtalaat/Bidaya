<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateEnrollmentStatusAction
{
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
            $enrollment->update([
                'status' => $newStatus,
                'end_date' => $validated['end_date'] ?? $enrollment->end_date,
            ]);

            if ($newStatus === EnrollmentStatus::Cancelled) {
                $enrollment->invoices()
                    ->where('status', InvoiceStatus::Pending)
                    ->update(['status' => InvoiceStatus::Cancelled]);
            }

            return $enrollment->refresh();
        });
    }
}
