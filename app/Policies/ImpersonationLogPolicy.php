<?php

namespace App\Policies;

use App\Models\ImpersonationLog;
use App\Models\User;

class ImpersonationLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('viewAny:ImpersonationLog');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImpersonationLog $impersonationLog): bool
    {
        return $user->can('view:ImpersonationLog');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create:ImpersonationLog');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImpersonationLog $impersonationLog): bool
    {
        return $user->can('update:ImpersonationLog');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImpersonationLog $impersonationLog): bool
    {
        return $user->can('delete:ImpersonationLog');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImpersonationLog $impersonationLog): bool
    {
        return $user->can('restore:ImpersonationLog');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImpersonationLog $impersonationLog): bool
    {
        return $user->can('forceDelete:ImpersonationLog');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('forceDeleteAny:ImpersonationLog');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restoreAny:ImpersonationLog');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImpersonationLog $impersonationLog): bool
    {
        return $user->can('replicate:ImpersonationLog');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder:ImpersonationLog');
    }
}
