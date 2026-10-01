<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Late = 'late';
    case Excused = 'excused';

    public function label(): string
    {
        return __("enums.attendance_statuses.{$this->value}");
    }

    public function countsAsAttended(): bool
    {
        return in_array($this, [self::Present, self::Late], true);
    }
}
