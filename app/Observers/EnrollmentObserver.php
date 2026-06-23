<?php

namespace App\Observers;

use App\Models\Enrollment;

class EnrollmentObserver
{
    /**
     * Handle the Enrollment "created" event.
     */
    public function created(Enrollment $enrollment): void
    {
        if ($enrollment->class_id) {
            $class = $enrollment->class;
            if ($class) {
                $class->increment('current_enrolled');
            }
        }
    }

    /**
     * Handle the Enrollment "updated" event.
     */
    public function updated(Enrollment $enrollment): void
    {
        //
    }

    /**
     * Handle the Enrollment "deleted" event.
     */
    public function deleted(Enrollment $enrollment): void
    {
        if ($enrollment->class_id && $enrollment->isForceDeleting() === false) {
            $class = $enrollment->class;
            if ($class && $class->current_enrolled > 0) {
                $class->decrement('current_enrolled');
            }
        }
    }

    /**
     * Handle the Enrollment "restored" event.
     */
    public function restored(Enrollment $enrollment): void
    {
        if ($enrollment->class_id) {
            $class = $enrollment->class;
            if ($class) {
                $class->increment('current_enrolled');
            }
        }
    }

    /**
     * Handle the Enrollment "force deleted" event.
     */
    public function forceDeleted(Enrollment $enrollment): void
    {
        if ($enrollment->class_id) {
            $class = $enrollment->class;
            if ($class && $class->current_enrolled > 0) {
                $class->decrement('current_enrolled');
            }
        }
    }
}
