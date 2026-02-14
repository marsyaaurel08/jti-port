<?php

namespace App\Observers;

use App\Models\UserCart;
use Illuminate\Support\Facades\Cache;

class UserCartObserver
{
    /**
     * Handle the UserCart "created" event.
     */
    public function created(UserCart $userCart): void
    {
        Cache::forget("user_cart_{$userCart->user_id}");
    }

    /**
     * Handle the UserCart "updated" event.
     */
    public function updated(UserCart $userCart): void
    {
        Cache::forget("user_cart_{$userCart->user_id}");
    }

    /**
     * Handle the UserCart "deleted" event.
     */
    public function deleted(UserCart $userCart): void
    {
        Cache::forget("user_cart_{$userCart->user_id}");
    }

    /**
     * Handle the UserCart "restored" event.
     */
    public function restored(UserCart $userCart): void
    {
        Cache::forget("user_cart_{$userCart->user_id}");
    }

    /**
     * Handle the UserCart "force deleted" event.
     */
    public function forceDeleted(UserCart $userCart): void
    {
        Cache::forget("user_cart_{$userCart->user_id}");
    }
}
