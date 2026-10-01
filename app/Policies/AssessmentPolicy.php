<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assessment $assessment): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant)
            || $this->teachesAssessmentGroup($user, $assessment);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager, Role::Teacher);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assessment $assessment): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager)
            || $this->teachesAssessmentGroup($user, $assessment);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager)
            || $this->teachesAssessmentGroup($user, $assessment);
    }

    private function teachesAssessmentGroup(User $user, Assessment $assessment): bool
    {
        return $user->hasRole(Role::Teacher) && $user->teacher?->groups()->whereKey($assessment->group_id)->exists();
    }
}
