<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CourseClass;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseClassPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CourseClass');
    }

    public function view(AuthUser $authUser, CourseClass $courseClass): bool
    {
        return $authUser->can('View:CourseClass');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CourseClass');
    }

    public function update(AuthUser $authUser, CourseClass $courseClass): bool
    {
        return $authUser->can('Update:CourseClass');
    }

    public function delete(AuthUser $authUser, CourseClass $courseClass): bool
    {
        return $authUser->can('Delete:CourseClass');
    }

    public function restore(AuthUser $authUser, CourseClass $courseClass): bool
    {
        return $authUser->can('Restore:CourseClass');
    }

    public function forceDelete(AuthUser $authUser, CourseClass $courseClass): bool
    {
        return $authUser->can('ForceDelete:CourseClass');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CourseClass');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CourseClass');
    }

    public function replicate(AuthUser $authUser, CourseClass $courseClass): bool
    {
        return $authUser->can('Replicate:CourseClass');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CourseClass');
    }

}