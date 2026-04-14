<?php

namespace App\Observers;

use App\Models\UserCart;
use Illuminate\Support\Facades\Cache;

class UserCartObserver
{
    public function created(UserCart $userCart): void
    {
        $this->clearCache($userCart->user_id);
    }

    public function updated(UserCart $userCart): void
    {
        $this->clearCache($userCart->user_id);
    }

    public function deleted(UserCart $userCart): void
    {
        $this->clearCache($userCart->user_id);
    }

    public function restored(UserCart $userCart): void
    {
        $this->clearCache($userCart->user_id);
    }

    public function forceDeleted(UserCart $userCart): void
    {
        $this->clearCache($userCart->user_id);
    }

    private function clearCache($userId): void
    {
        Cache::forget("user_cart_items_{$userId}");
        Cache::forget("user_cart_count_{$userId}");
    }
}
