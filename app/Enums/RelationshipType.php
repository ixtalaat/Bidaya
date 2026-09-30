<?php

namespace App\Enums;

enum RelationshipType: string
{
    case Father = 'father';
    case Mother = 'mother';
    case Guardian = 'guardian';
    case Other = 'other';

    public function label(): string
    {
        return __("enums.relationships.{$this->value}");
    }
}
