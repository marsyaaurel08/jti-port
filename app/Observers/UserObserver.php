<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Clear staff status cache when user is updated (roles might have changed)
        Cache::forget("user_staff_{$user->id}");
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Cache::forget("user_staff_{$user->id}");
        Cache::forget("user_cart_{$user->id}");
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        Cache::forget("user_staff_{$user->id}");
        Cache::forget("user_cart_{$user->id}");
    }
}
