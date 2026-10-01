<?php

namespace App\Services;

use App\Contracts\WhatsAppGateway;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class MetaWhatsAppGateway implements WhatsAppGateway
{
    public function __construct(
        private string $phoneNumberId,
        private string $token,
        private bool $enabled,
    ) {}

    public function enabled(): bool
    {
        return $this->enabled && $this->phoneNumberId !== '' && $this->token !== '';
    }

    public function send(string $to, string $message): WhatsAppResult
    {
        if (! $this->enabled()) {
            return WhatsAppResult::failed('WhatsApp sending is disabled.');
        }

        try {
            $response = Http::timeout(10)->withToken($this->token)->post(
                "https://graph.facebook.com/v22.0/{$this->phoneNumberId}/messages",
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['preview_url' => false, 'body' => $message],
                ]
            );
        } catch (ConnectionException $exception) {
            return WhatsAppResult::failed($exception->getMessage());
        }

        if ($response->failed()) {
            return WhatsAppResult::failed(
                $response->json('error.message') ?? "WhatsApp API responded with HTTP {$response->status()}."
            );
        }

        return WhatsAppResult::sent((string) $response->json('messages.0.id'));
    }
}
