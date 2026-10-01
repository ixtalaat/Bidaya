<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';

    public function label(): string
    {
        return __("enums.assignment_statuses.{$this->value}");
    }

    public function isOpenForSubmissions(): bool
    {
        return $this === self::Published;
    }

    /**
     * @return array<int, AssignmentStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Published],
            self::Published => [self::Closed],
            self::Closed => [],
        };
    }
}
