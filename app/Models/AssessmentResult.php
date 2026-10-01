<?php

namespace App\Models;

use Database\Factories\AssessmentResultFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    /** @use HasFactory<AssessmentResultFactory> */
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'enrollment_id',
        'score',
        'feedback',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    protected function percentage(): Attribute
    {
        return Attribute::get(function (): ?float {
            $maxScore = (float) $this->assessment->max_score;

            if ($maxScore <= 0) {
                return null;
            }

            return round((float) $this->score / $maxScore * 100, 2);
        });
    }
}
