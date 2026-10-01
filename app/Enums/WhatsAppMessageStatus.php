<?php

namespace App\Enums;

enum WhatsAppMessageStatus: string
{
    case Sent = 'sent';
    case Failed = 'failed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return __("enums.whatsapp_message_statuses.{$this->value}");
    }
}
