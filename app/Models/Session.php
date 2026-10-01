<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Database\Factories\SessionFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    /** @use HasFactory<SessionFactory> */
    use HasFactory;

    protected $table = 'class_sessions';

    protected $fillable = [
        'group_id',
        'teacher_id',
        'date',
        'start_time',
        'end_time',
        'classroom',
        'status',
        'teacher_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => SessionStatus::class,
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    #[Scope]
    protected function upcoming(Builder $query): Builder
    {
        return $query->where('status', SessionStatus::Scheduled)->orderBy('date')->orderBy('start_time')->orderBy('id');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [SessionStatus::Scheduled, SessionStatus::Postponed], true);
    }
}
