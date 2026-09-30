<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'min_age',
        'max_age',
        'level',
        'duration_weeks',
        'sessions_count',
        'monthly_fee',
        'status',
        'prerequisites',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_fee' => 'decimal:2',
            'status' => CourseStatus::class,
        ];
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
