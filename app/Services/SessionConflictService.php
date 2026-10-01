<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Models\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SessionConflictService
{
    /**
     * Find non-cancelled sessions overlapping the given slot for the same teacher or classroom.
     *
     * @return Collection<int, Session>
     */
    public function findConflicts(string $date, string $startTime, string $endTime, int $teacherId, ?string $classroom, ?int $ignoreSessionId = null): Collection
    {
        return Session::query()
            ->whereDate('date', $date)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->where('status', '!=', SessionStatus::Cancelled)
            ->when(
                $ignoreSessionId,
                fn (Builder $query, int $id) => $query->where('id', '!=', $id)
            )
            ->where(fn (Builder $query) => $query
                ->where('teacher_id', $teacherId)
                ->when(
                    $classroom,
                    fn (Builder $query, string $room) => $query->orWhere('classroom', $room)
                ))
            ->with(['group:id,name'])
            ->orderBy('id')
            ->get();
    }
}
