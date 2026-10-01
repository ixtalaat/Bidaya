<?php

namespace App\Actions\Attendance;

use App\Enums\SessionStatus;
use App\Models\Attendance;
use App\Models\Session;
use Illuminate\Support\Carbon;
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

        return count($rows);
    }
}
