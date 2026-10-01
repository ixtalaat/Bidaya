<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Session;
use App\Models\User;

class SessionPolicy
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
    public function view(User $user, Session $session): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager, Role::Accountant)
            || $this->ownsSession($user, $session);
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
    public function update(User $user, Session $session): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager)
            || $this->ownsSession($user, $session);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Session $session): bool
    {
        return $user->hasRole(Role::SuperAdmin, Role::Manager);
    }

    private function ownsSession(User $user, Session $session): bool
    {
        return $user->hasRole(Role::Teacher) && $user->teacher?->id === $session->teacher_id;
    }
}
