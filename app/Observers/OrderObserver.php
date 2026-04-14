<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $cacheKey = "user:{$order->user_id}:unpaid_orders";
        Cache::forget($cacheKey);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {

            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // hanya invalidate jika terkait pending
            if ($oldStatus === 'pending' || $newStatus === 'pending') {
                Cache::forget("user:{$order->user_id}:unpaid_orders");
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $cacheKey = "user:{$order->user_id}:unpaid_orders";
        Cache::forget($cacheKey);
    }
    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
