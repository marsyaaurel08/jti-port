<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LogSelfHealing;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LogSelfHealingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LogSelfHealing');
    }

    public function view(AuthUser $authUser, LogSelfHealing $logSelfHealing): bool
    {
        return $authUser->can('View:LogSelfHealing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LogSelfHealing');
    }

    public function update(AuthUser $authUser, LogSelfHealing $logSelfHealing): bool
    {
        return $authUser->can('Update:LogSelfHealing');
    }

    public function delete(AuthUser $authUser, LogSelfHealing $logSelfHealing): bool
    {
        return $authUser->can('Delete:LogSelfHealing');
    }

    public function restore(AuthUser $authUser, LogSelfHealing $logSelfHealing): bool
    {
        return $authUser->can('Restore:LogSelfHealing');
    }

    public function forceDelete(AuthUser $authUser, LogSelfHealing $logSelfHealing): bool
    {
        return $authUser->can('ForceDelete:LogSelfHealing');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LogSelfHealing');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LogSelfHealing');
    }

    public function replicate(AuthUser $authUser, LogSelfHealing $logSelfHealing): bool
    {
        return $authUser->can('Replicate:LogSelfHealing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LogSelfHealing');
    }
}
