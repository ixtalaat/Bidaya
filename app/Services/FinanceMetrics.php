<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;

class FinanceMetrics
{
    public function outstandingTotal(): float
    {
        $invoiced = (float) Invoice::where('status', '!=', InvoiceStatus::Cancelled)->sum('total');
        $collected = (float) Payment::where('status', PaymentStatus::Completed)->sum('amount');

        return max(0, round($invoiced - $collected, 2));
    }

    public function collectedBetween(string $from, string $to): float
    {
        return round((float) Payment::where('status', PaymentStatus::Completed)
            ->whereDate('payment_date', '>=', $from)
            ->whereDate('payment_date', '<=', $to)
            ->sum('amount'), 2);
    }
}
