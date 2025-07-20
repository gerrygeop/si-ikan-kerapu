<?php

namespace App\Policies;

use App\Models\Fish;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FishPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['operator', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fish $fish): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fish $fish): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fish $fish): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fish $fish): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fish $fish): bool
    {
        return $this->viewAny($user);
    }
}
