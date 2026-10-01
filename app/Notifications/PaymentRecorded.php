<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentRecorded extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Payment $payment) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $studentName = $this->payment->invoice->enrollment->student->name;
        $amount = number_format($this->payment->amount, 2);

        return [
            'type' => 'payment.recorded',
            'message_en' => "A payment of {$amount} EGP was recorded for {$studentName}.",
            'message_ar' => "تم تسجيل دفعة بمبلغ {$amount} جنيه لـ {$studentName}.",
            'url' => route('payments.show', $this->payment),
        ];
    }
}
