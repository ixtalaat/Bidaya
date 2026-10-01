<?php

namespace App\Policies;

use App\Enums\AssignmentStatus;
use App\Enums\Role;
use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
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
    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant)) {
            return true;
        }

        if ($this->teachesGroup($user, $assignment->group_id)) {
            return true;
        }

        return $assignment->status !== AssignmentStatus::Draft
            && $this->linkedToGroup($user, $assignment->group_id);
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
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager)
            || $this->teachesGroup($user, $assignment->group_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager)
            || $this->teachesGroup($user, $assignment->group_id);
    }

    private function teachesGroup(User $user, int $groupId): bool
    {
        return $user->hasRole(Role::Teacher) && $user->teacher?->groups()->whereKey($groupId)->exists();
    }

    private function linkedToGroup(User $user, int $groupId): bool
    {
        $guardian = $user->guardian;

        if ($guardian !== null && $guardian->students()->whereHas(
            'enrollments',
            fn ($query) => $query->active()->where('group_id', $groupId)
        )->exists()) {
            return true;
        }

        return $user->student?->enrollments()->active()->where('group_id', $groupId)->exists() ?? false;
    }
}
