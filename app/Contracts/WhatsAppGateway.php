<?php

namespace App\Contracts;

use App\Services\WhatsAppResult;

interface WhatsAppGateway
{
    public function send(string $to, string $message): WhatsAppResult;

    public function enabled(): bool;
}
