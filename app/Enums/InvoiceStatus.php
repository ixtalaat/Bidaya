<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __("enums.invoice_statuses.{$this->value}");
    }
}
