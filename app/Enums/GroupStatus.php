<?php

namespace App\Enums;

enum GroupStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __("enums.group_statuses.{$this->value}");
    }

    public function acceptsEnrollments(): bool
    {
        return $this === self::Active;
    }
}
