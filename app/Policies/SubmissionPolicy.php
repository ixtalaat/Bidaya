<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Determine whether the user can submit homework for the enrollment.
     */
    public function create(User $user, Enrollment $enrollment): bool
    {
        return $enrollment->isActive() && $this->linkedToEnrollment($user, $enrollment);
    }

    /**
     * Determine whether the user can resubmit the submission.
     */
    public function update(User $user, Submission $submission): bool
    {
        if (! $submission->assignment->isOpenForSubmissions()) {
            return false;
        }

        if (! $submission->enrollment->isActive()) {
            return false;
        }

        if (! $this->linkedToEnrollment($user, $submission->enrollment)) {
            return false;
        }

        return $submission->submitted_by === null
            || $submission->submitted_by === $user->id
            || $this->isParentOf($user, $submission->enrollment);
    }

    private function linkedToEnrollment(User $user, Enrollment $enrollment): bool
    {
        $guardian = $user->guardian;

        if ($guardian !== null && $guardian->students()->where('students.id', $enrollment->student_id)->exists()) {
            return true;
        }

        return $user->student?->id === $enrollment->student_id;
    }

    private function isParentOf(User $user, Enrollment $enrollment): bool
    {
        $guardian = $user->guardian;

        return $guardian !== null
            && $guardian->students()->where('students.id', $enrollment->student_id)->exists();
    }
}
