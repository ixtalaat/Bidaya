<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Graduated = 'graduated';
    case Archived = 'archived';

    public function label(): string
    {
        return __("enums.student_statuses.{$this->value}");
    }

    public function isEnrollable(): bool
    {
        return $this === self::Active;
    }
}
