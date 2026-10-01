<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Group;
use App\Models\User;

class GroupPolicy
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
    public function view(User $user, Group $group): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant)
            || $this->teachesGroup($user, $group);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Group $group): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager);
    }

    private function teachesGroup(User $user, Group $group): bool
    {
        return $user->hasRole(Role::Teacher) && $user->teacher?->groups()->whereKey($group->id)->exists();
    }
}
