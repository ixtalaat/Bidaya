<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
        'enrollment_date',
        'start_date',
        'end_date',
        'monthly_fee',
        'discount_amount',
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
            'enrollment_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'monthly_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'status' => EnrollmentStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereIn('status', [
            EnrollmentStatus::Pending,
            EnrollmentStatus::Active,
            EnrollmentStatus::Suspended,
        ]);
    }

    protected function finalMonthlyFee(): Attribute
    {
        return Attribute::get(fn (): string => number_format(max(0, (float) $this->monthly_fee - (float) $this->discount_amount), 2, '.', ''));
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
