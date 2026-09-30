<?php

namespace App\Enums;

enum TeacherStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return __("enums.teacher_statuses.{$this->value}");
    }
}
