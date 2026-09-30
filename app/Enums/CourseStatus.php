<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Active = 'active';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return __("enums.course_statuses.{$this->value}");
    }

    public function acceptsEnrollments(): bool
    {
        return in_array($this, [self::Published, self::Active], true);
    }
}
