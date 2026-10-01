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
        if ($user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant)) {
            return true;
        }

        if ($this->teachesGroup($user, $group)) {
            return true;
        }

        return $this->linkedToGroup($user, $group->id);
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
