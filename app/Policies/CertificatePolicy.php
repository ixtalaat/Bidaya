<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        if ($user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant)) {
            return true;
        }

        if ($this->teachesEnrollment($user, $certificate->enrollment)) {
            return true;
        }

        return $this->linkedToEnrollment($user, $certificate->enrollment);
    }

    /**
     * Determine whether the user can issue a certificate for the enrollment.
     */
    public function issue(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager)
            || $this->teachesEnrollment($user, $enrollment);
    }

    private function teachesEnrollment(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole(Role::Teacher)
            && $user->teacher?->groups()->whereKey($enrollment->group_id)->exists();
    }

    private function linkedToEnrollment(User $user, Enrollment $enrollment): bool
    {
        $guardian = $user->guardian;

        if ($guardian !== null && $guardian->students()->where('students.id', $enrollment->student_id)->exists()) {
            return true;
        }

        return $user->student?->id === $enrollment->student_id;
    }
}
