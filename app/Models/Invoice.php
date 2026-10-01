<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'period_start',
        'period_end',
        'amount',
        'discount_amount',
        'total',
        'status',
        'due_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paidTotal(): float
    {
        return round($this->payments()->where('status', PaymentStatus::Completed)->sum('amount'), 2);
    }

    public function remaining(): float
    {
        return max(0, round((float) $this->total - $this->paidTotal(), 2));
    }

    public function refreshStatus(): void
    {
        if ($this->status === InvoiceStatus::Cancelled) {
            return;
        }

        $remaining = $this->remaining();

        $this->status = match (true) {
            $remaining <= 0 => InvoiceStatus::Paid,
            $this->paidTotal() > 0 => InvoiceStatus::PartiallyPaid,
            default => InvoiceStatus::Pending,
        };

        if ($this->isDirty('status')) {
            $this->save();
        }
    }
}
