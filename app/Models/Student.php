<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'date_of_birth',
        'gender',
        'address',
        'school',
        'grade_level',
        'status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender' => Gender::class,
            'status' => StudentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'parent_student', 'student_id', 'parent_id')
            ->using(ParentStudent::class)
            ->withPivot('relationship')
            ->withTimestamps();
    }

    #[Scope]
    protected function search(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    #[Scope]
    protected function withStatus(Builder $query, StudentStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    protected function age(): Attribute
    {
        return Attribute::get(fn (): ?int => $this->date_of_birth?->age);
    }

    public function isEnrollable(): bool
    {
        return $this->status->isEnrollable();
    }
}
