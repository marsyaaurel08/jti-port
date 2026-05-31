<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ClassResult;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassResultPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ClassResult');
    }

    public function view(AuthUser $authUser, ClassResult $classResult): bool
    {
        return $authUser->can('View:ClassResult');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ClassResult');
    }

    public function update(AuthUser $authUser, ClassResult $classResult): bool
    {
        return $authUser->can('Update:ClassResult');
    }

    public function delete(AuthUser $authUser, ClassResult $classResult): bool
    {
        return $authUser->can('Delete:ClassResult');
    }

    public function restore(AuthUser $authUser, ClassResult $classResult): bool
    {
        return $authUser->can('Restore:ClassResult');
    }

    public function forceDelete(AuthUser $authUser, ClassResult $classResult): bool
    {
        return $authUser->can('ForceDelete:ClassResult');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ClassResult');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ClassResult');
    }

    public function replicate(AuthUser $authUser, ClassResult $classResult): bool
    {
        return $authUser->can('Replicate:ClassResult');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ClassResult');
    }

}