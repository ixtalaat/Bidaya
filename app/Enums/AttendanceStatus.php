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

    /**
     * @return array<int, string>
     */
    public static function attendedValues(): array
    {
        return [self::Present->value, self::Late->value];
    }

    public function countsAsAttended(): bool
    {
        return in_array($this->value, self::attendedValues(), true);
    }
}
