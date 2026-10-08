<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Manager = 'manager';
    case Teacher = 'teacher';
    case Accountant = 'accountant';
    case Parent = 'parent';
    case Student = 'student';

    public function label(): string
    {
        return __("roles.{$this->value}");
    }
}
