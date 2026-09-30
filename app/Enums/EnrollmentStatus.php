<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __("enums.enrollment_statuses.{$this->value}");
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::Active, self::Suspended], true);
    }

    /**
     * @return array<int, EnrollmentStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Active, self::Cancelled],
            self::Active => [self::Suspended, self::Completed, self::Cancelled],
            self::Suspended => [self::Active, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }
}
