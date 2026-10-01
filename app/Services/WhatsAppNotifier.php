<?php

namespace App\Services;

use App\Contracts\WhatsAppGateway;
use App\Enums\WhatsAppMessageStatus;
use App\Models\Student;
use App\Models\WhatsAppMessage;
use App\Support\PhoneNumber;
use Throwable;

class WhatsAppNotifier
{
    public function __construct(private WhatsAppGateway $gateway) {}

    /**
     * Message every guardian phone of the student. Never throws.
     */
    public function notifyGuardians(Student $student, string $event, string $message): void
    {
        $phones = $student->guardians()->pluck('parents.phone')->unique()->all();

        foreach ($phones as $phone) {
            $this->notifyPhone($phone, $event, $message);
        }
    }

    /**
     * Send one message, logging every attempt. Never throws.
     */
    public function notifyPhone(?string $phone, string $event, string $message): void
    {
        $recipient = PhoneNumber::normalize($phone);

        if ($recipient === null) {
            WhatsAppMessage::create([
                'recipient' => (string) $phone,
                'body' => $message,
                'event' => $event,
                'status' => WhatsAppMessageStatus::Skipped,
                'error' => 'Invalid or missing phone number.',
            ]);

            return;
        }

        try {
            $result = $this->gateway->send($recipient, $message);
        } catch (Throwable $exception) {
            $result = WhatsAppResult::failed($exception->getMessage());
        }

        WhatsAppMessage::create([
            'recipient' => $recipient,
            'body' => $message,
            'event' => $event,
            'status' => $result->sent ? WhatsAppMessageStatus::Sent : WhatsAppMessageStatus::Failed,
            'provider_message_id' => $result->providerMessageId,
            'error' => $result->error,
        ]);
    }
}
