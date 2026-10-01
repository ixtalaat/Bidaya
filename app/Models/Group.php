<?php

namespace App\Models;

use App\Enums\GroupStatus;
use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'name',
        'capacity',
        'classroom',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => GroupStatus::class,
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->active();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(GroupSchedule::class)->orderBy('day_of_week')->orderBy('start_time');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class)->orderBy('date')->orderBy('start_time');
    }

    public function availableSeats(): int
    {
        return max(0, $this->capacity - $this->activeEnrollments()->count());
    }
}
