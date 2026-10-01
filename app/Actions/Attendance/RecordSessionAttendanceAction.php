<?php

namespace App\Actions\Attendance;

use App\Enums\AttendanceStatus;
use App\Enums\SessionStatus;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Session;
use App\Notifications\StudentAbsent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class RecordSessionAttendanceAction
{
    /**
     * Record attendance for a session in a single atomic upsert.
     *
     * @param  array<int, array{enrollment_id: int, status: string}>  $records
     *
     * @throws ValidationException
     */
    public function handle(Session $session, array $records, int $recordedBy, bool $isStaff): int
    {
        if ($session->status === SessionStatus::Cancelled) {
            throw ValidationException::withMessages([
                'session' => __('attendance.cancelled_session'),
            ]);
        }

        if ($session->status === SessionStatus::Completed && ! $isStaff) {
            throw ValidationException::withMessages([
                'session' => __('attendance.completed_locked'),
            ]);
        }

        $activeEnrollmentIds = $session->group->enrollments()->active()->pluck('enrollments.id')->all();

        $unknownIds = collect($records)
            ->pluck('enrollment_id')
            ->diff($activeEnrollmentIds)
            ->values()
            ->all();

        if ($unknownIds !== []) {
            throw ValidationException::withMessages([
                'records' => __('attendance.unknown_enrollments'),
            ]);
        }

        $now = Carbon::now()->toDateTimeString();

        $rows = array_map(fn (array $record): array => [
            'session_id' => $session->id,
            'enrollment_id' => $record['enrollment_id'],
            'status' => $record['status'],
            'recorded_by' => $recordedBy,
            'created_at' => $now,
            'updated_at' => $now,
        ], $records);

        Attendance::upsert($rows, ['session_id', 'enrollment_id'], ['status', 'recorded_by', 'updated_at']);

        $this->notifyAbsentGuardians($session, $records);

        return count($rows);
    }

    /**
     * @param  array<int, array{enrollment_id: int|string, status: string}>  $records
     */
    private function notifyAbsentGuardians(Session $session, array $records): void
    {
        $absentEnrollmentIds = collect($records)
            ->where('status', AttendanceStatus::Absent->value)
            ->pluck('enrollment_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($absentEnrollmentIds === []) {
            return;
        }

        $session->load('group');

        $absentEnrollments = Enrollment::whereIn('id', $absentEnrollmentIds)->with('student')->get();

        foreach ($absentEnrollments as $enrollment) {
            Notification::send(
                Guardian::notifiableUsersForStudent($enrollment->student),
                new StudentAbsent($session, $enrollment->student->name)
            );
        }
    }
}
