<?php

namespace App\Policies;

use App\Models\HelpDesk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HelpDeskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'super admin', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HelpDesk $helpDesk): bool
    {
        return $user->hasRole(['admin', 'super admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HelpDesk $helpDesk): bool
    {
        return $user->hasRole(['admin', 'super admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HelpDesk $helpDesk): bool
    {
        return $user->hasRole(['admin', 'super admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HelpDesk $helpDesk): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HelpDesk $helpDesk): bool
    {
        return false;
    }
}
