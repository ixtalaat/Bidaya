<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use App\Notifications\StudentEnrolled;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class EnrollStudentAction
{
    public function __construct(private AuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws ValidationException
     */
    public function handle(array $validated): Enrollment
    {
        return DB::transaction(function () use ($validated): Enrollment {
            $student = Student::findOrFail($validated['student_id']);
            $group = Group::lockForUpdate()->with(['course', 'teacher'])->findOrFail($validated['group_id']);

            if (! $student->isEnrollable()) {
                throw ValidationException::withMessages([
                    'student_id' => __('enrollments.student_not_active'),
                ]);
            }

            if (! $group->status->acceptsEnrollments()) {
                throw ValidationException::withMessages([
                    'group_id' => __('enrollments.group_not_active'),
                ]);
            }

            if (! $group->course->status->acceptsEnrollments()) {
                throw ValidationException::withMessages([
                    'group_id' => __('enrollments.course_not_active'),
                ]);
            }

            if (! $group->teacher->isActive()) {
                throw ValidationException::withMessages([
                    'group_id' => __('enrollments.teacher_not_active'),
                ]);
            }

            if ($group->enrollments()->active()->where('student_id', $student->id)->exists()) {
                throw ValidationException::withMessages([
                    'student_id' => __('enrollments.already_enrolled'),
                ]);
            }

            if ($group->activeEnrollments()->count() >= $group->capacity) {
                throw ValidationException::withMessages([
                    'group_id' => __('enrollments.group_full'),
                ]);
            }

            $monthlyFee = (float) $group->course->monthly_fee;
            $discount = (float) ($validated['discount_amount'] ?? 0);

            if ($discount > $monthlyFee) {
                throw ValidationException::withMessages([
                    'discount_amount' => __('enrollments.discount_exceeds_fee'),
                ]);
            }

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'group_id' => $group->id,
                'enrollment_date' => $validated['enrollment_date'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'monthly_fee' => $monthlyFee,
                'discount_amount' => $discount,
                'status' => EnrollmentStatus::Active,
            ]);

            $periodStart = $enrollment->start_date->copy();
            $periodEnd = $periodStart->copy()->addMonth()->subDay();

            $enrollment->invoices()->create([
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'amount' => $monthlyFee,
                'discount_amount' => $discount,
                'total' => max(0, $monthlyFee - $discount),
                'status' => InvoiceStatus::Pending,
                'due_date' => $periodStart->toDateString(),
            ]);

            $this->audit->log(auth()->user(), 'enrollment.created', $enrollment, [], $enrollment->toArray());

            $enrollment->load(['student', 'group.course']);

            Notification::send(
                Guardian::notifiableUsersForStudent($enrollment->student),
                new StudentEnrolled($enrollment)
            );

            return $enrollment;
        });
    }
}
