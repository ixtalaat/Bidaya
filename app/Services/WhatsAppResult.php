<?php

namespace App\Services;

final class WhatsAppResult
{
    public function __construct(
        public readonly bool $sent,
        public readonly ?string $providerMessageId = null,
        public readonly ?string $error = null,
    ) {}

    public static function sent(string $providerMessageId): self
    {
        return new self(true, $providerMessageId);
    }

    public static function failed(string $error): self
    {
        return new self(false, null, $error);
    }
}
