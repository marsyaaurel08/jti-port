<?php

namespace App\Listeners;

use App\Models\ImpersonationLog;
use Lab404\Impersonate\Events\TakeImpersonation;

class LogImpersonationStarted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TakeImpersonation $event): void
    {
        ImpersonationLog::create([
            'impersonator_id' => $event->impersonator->id,
            'impersonated_id' => $event->impersonated->id,
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
