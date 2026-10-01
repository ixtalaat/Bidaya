<?php

namespace App\Actions\Payments;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordPaymentAction
{
    public function __construct(private AuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws ValidationException
     */
    public function handle(array $validated, User $receiver): Payment
    {
        return DB::transaction(function () use ($validated, $receiver): Payment {
            $invoice = Invoice::lockForUpdate()->findOrFail($validated['invoice_id']);

            if ($invoice->status === InvoiceStatus::Cancelled) {
                throw ValidationException::withMessages([
                    'invoice_id' => __('payments.invoice_cancelled'),
                ]);
            }

            $amount = round((float) $validated['amount'], 2);

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => __('payments.amount_invalid'),
                ]);
            }

            $remaining = $invoice->remaining();

            if ($amount > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => __('payments.overpayment', ['remaining' => number_format($remaining, 2)]),
                ]);
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'],
                'reference' => $validated['reference'] ?? null,
                'received_by' => $receiver->id,
                'notes' => $validated['notes'] ?? null,
                'status' => PaymentStatus::Completed,
            ]);

            $invoice->refreshStatus();

            $this->audit->log($receiver, 'payment.recorded', $payment, [], $payment->toArray());

            return $payment;
        });
    }
}
