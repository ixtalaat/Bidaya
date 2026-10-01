<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Submitted = 'submitted';
    case Late = 'late';
    case Reviewed = 'reviewed';

    public function label(): string
    {
        return __("enums.submission_statuses.{$this->value}");
    }
}
