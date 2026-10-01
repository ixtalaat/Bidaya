<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Database\Factories\GroupScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSchedule extends Model
{
    /** @use HasFactory<GroupScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'group_id',
        'day_of_week',
        'start_time',
        'end_time',
        'classroom',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day_of_week' => DayOfWeek::class,
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
