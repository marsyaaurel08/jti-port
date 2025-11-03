<?php

namespace App\Listeners;

use App\Models\ImpersonationLog;
use Lab404\Impersonate\Events\LeaveImpersonation;

class LogImpersonationEnded
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
    public function handle(LeaveImpersonation $event): void
    {
        ImpersonationLog::where('impersonator_id', $event->impersonator->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first()
            ?->update(['ended_at' => now()]);
    }
}
