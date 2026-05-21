<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HomeInstructor;
use Illuminate\Auth\Access\HandlesAuthorization;

class HomeInstructorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HomeInstructor');
    }

    public function view(AuthUser $authUser, HomeInstructor $homeInstructor): bool
    {
        return $authUser->can('View:HomeInstructor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HomeInstructor');
    }

    public function update(AuthUser $authUser, HomeInstructor $homeInstructor): bool
    {
        return $authUser->can('Update:HomeInstructor');
    }

    public function delete(AuthUser $authUser, HomeInstructor $homeInstructor): bool
    {
        return $authUser->can('Delete:HomeInstructor');
    }

    public function restore(AuthUser $authUser, HomeInstructor $homeInstructor): bool
    {
        return $authUser->can('Restore:HomeInstructor');
    }

    public function forceDelete(AuthUser $authUser, HomeInstructor $homeInstructor): bool
    {
        return $authUser->can('ForceDelete:HomeInstructor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HomeInstructor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HomeInstructor');
    }

    public function replicate(AuthUser $authUser, HomeInstructor $homeInstructor): bool
    {
        return $authUser->can('Replicate:HomeInstructor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HomeInstructor');
    }

}