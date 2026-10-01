<?php

namespace App\Actions\Payments;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentRecorded;
use App\Services\AuditLogger;
use App\Services\WhatsAppNotifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class RecordPaymentAction
{
    public function __construct(private AuditLogger $audit, private WhatsAppNotifier $whatsapp) {}

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

            $payment->load('invoice.enrollment.student');

            Notification::send(
                Guardian::notifiableUsersForStudent($payment->invoice->enrollment->student),
                new PaymentRecorded($payment)
            );

            $this->whatsapp->notifyGuardians(
                $payment->invoice->enrollment->student,
                'payment.recorded',
                __('whatsapp.payment_recorded', [
                    'amount' => number_format($payment->amount, 2),
                    'student' => $payment->invoice->enrollment->student->name,
                    'remaining' => number_format($payment->invoice->remaining(), 2),
                ], 'ar')
            );

            return $payment;
        });
    }
}
