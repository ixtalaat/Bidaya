<?php

namespace App\Enums;

enum SessionStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Postponed = 'postponed';

    public function label(): string
    {
        return __("enums.session_statuses.{$this->value}");
    }

    /**
     * @return array<int, SessionStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Scheduled => [self::Completed, self::Cancelled, self::Postponed],
            self::Postponed => [self::Scheduled, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }
}
