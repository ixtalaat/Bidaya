<?php

namespace App\Actions\Sessions;

use App\Enums\DayOfWeek;
use App\Enums\GroupStatus;
use App\Enums\SessionStatus;
use App\Models\Group;
use App\Models\Session;
use App\Services\SessionConflictService;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenerateGroupSessionsAction
{
    public function __construct(private SessionConflictService $conflicts) {}

    /**
     * Expand a group's weekly schedule into sessions for the given range.
     *
     * @return array{created: int, skipped: array<int, array{date: string, time: string, reason: string}>}
     *
     * @throws ValidationException
     */
    public function handle(Group $group, string $startDate, string $endDate): array
    {
        $preview = $this->preview($group, $startDate, $endDate);

        if ($preview['rows'] !== []) {
            $now = Carbon::now()->toDateTimeString();

            $rows = array_map(fn (array $row): array => [
                'group_id' => $group->id,
                'teacher_id' => $group->teacher_id,
                'date' => $row['date'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'classroom' => $row['classroom'],
                'status' => SessionStatus::Scheduled,
                'created_at' => $now,
                'updated_at' => $now,
            ], $preview['rows']);

            DB::transaction(fn () => Session::insert($rows));
        }

        return ['created' => count($preview['rows']), 'skipped' => $preview['skipped']];
    }

    /**
     * Compute the sessions that would be generated, without persisting them.
     *
     * @return array{rows: array<int, array{date: string, day: string, start_time: string, end_time: string, classroom: ?string}>, skipped: array<int, array{date: string, time: string, reason: string}>}
     *
     * @throws ValidationException
     */
    public function preview(Group $group, string $startDate, string $endDate): array
    {
        if (! in_array($group->status, [GroupStatus::Draft, GroupStatus::Active], true)) {
            throw ValidationException::withMessages([
                'group' => __('sessions.group_closed'),
            ]);
        }

        $schedules = $group->schedules;

        if ($schedules->isEmpty()) {
            throw ValidationException::withMessages([
                'group' => __('sessions.no_schedules'),
            ]);
        }

        $rows = [];
        $skipped = [];

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $daySchedules = $schedules->where('day_of_week', DayOfWeek::from((int) $date->format('w')));

            foreach ($daySchedules as $schedule) {
                $dateString = $date->toDateString();
                $startTime = substr((string) $schedule->start_time, 0, 5);
                $endTime = substr((string) $schedule->end_time, 0, 5);

                $duplicate = Session::where('group_id', $group->id)
                    ->whereDate('date', $dateString)
                    ->where('start_time', $schedule->start_time)
                    ->exists();

                if ($duplicate) {
                    $skipped[] = ['date' => $dateString, 'time' => $startTime, 'reason' => __('sessions.skipped_duplicate')];

                    continue;
                }

                $classroom = $schedule->classroom ?? $group->classroom;

                $conflicts = $this->conflicts->findConflicts(
                    $dateString,
                    $startTime,
                    $endTime,
                    $group->teacher_id,
                    $classroom
                );

                if ($conflicts->isNotEmpty()) {
                    $skipped[] = ['date' => $dateString, 'time' => $startTime, 'reason' => __('sessions.skipped_conflict')];

                    continue;
                }

                $rows[] = [
                    'date' => $dateString,
                    'day' => DayOfWeek::from((int) $date->format('w'))->label(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'classroom' => $classroom,
                ];
            }
        }

        return ['rows' => $rows, 'skipped' => $skipped];
    }
}
