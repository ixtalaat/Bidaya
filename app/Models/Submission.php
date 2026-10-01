<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'enrollment_id',
        'answer',
        'file_path',
        'submitted_by',
        'submitted_at',
        'score',
        'feedback',
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
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
            'status' => SubmissionStatus::class,
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    protected function percentage(): Attribute
    {
        return Attribute::get(function (): ?float {
            if ($this->score === null) {
                return null;
            }

            $maxScore = (float) $this->assignment->max_score;

            if ($maxScore <= 0) {
                return null;
            }

            return round((float) $this->score / $maxScore * 100, 2);
        });
    }
}
