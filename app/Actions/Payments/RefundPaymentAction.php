<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundPaymentAction
{
    public function __construct(private AuditLogger $audit) {}

    /**
     * @throws ValidationException
     */
    public function handle(Payment $payment, User $user): Payment
    {
        if ($payment->status !== PaymentStatus::Completed) {
            throw ValidationException::withMessages([
                'payment' => __('payments.refund_invalid_status'),
            ]);
        }

        return DB::transaction(function () use ($payment, $user): Payment {
            $before = $payment->toArray();

            $payment->update(['status' => PaymentStatus::Refunded]);

            $payment->invoice->refreshStatus();

            $this->audit->log($user, 'payment.refunded', $payment, $before, $payment->fresh()->toArray());

            return $payment->refresh();
        });
    }
}
