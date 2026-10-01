<?php

namespace App\Enums;

enum AssessmentType: string
{
    case Quiz = 'quiz';
    case Homework = 'homework';
    case Midterm = 'midterm';
    case Final = 'final';
    case Practical = 'practical';
    case Participation = 'participation';
    case Project = 'project';

    public function label(): string
    {
        return __("enums.assessment_types.{$this->value}");
    }
}
