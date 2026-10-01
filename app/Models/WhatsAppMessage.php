<?php

namespace App\Models;

use App\Enums\WhatsAppMessageStatus;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $fillable = [
        'recipient',
        'body',
        'event',
        'status',
        'error',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => WhatsAppMessageStatus::class,
        ];
    }
}
