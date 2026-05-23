<?php

namespace App\Observers;

use App\Models\CourseClass;
use Illuminate\Support\Facades\Cache;

class CourseClassObserver
{
    /**
     * Handle the CourseClass "created" event.
     */
    public function created(CourseClass $courseClass): void
    {
        //
    }

    /**
     * Handle the CourseClass "updated" event.
     */
    public function updated(CourseClass $courseClass): void
    {
        // Invalidate all course class related caches when course class is updated
        Cache::forget("class_checkout:{$courseClass->slug}");
        Cache::forget("class_detail:{$courseClass->slug}");
    }

    /**
     * Handle the CourseClass "deleted" event.
     */
    public function deleted(CourseClass $courseClass): void
    {
        //
    }

    /**
     * Handle the CourseClass "restored" event.
     */
    public function restored(CourseClass $courseClass): void
    {
        //
    }

    /**
     * Handle the CourseClass "force deleted" event.
     */
    public function forceDeleted(CourseClass $courseClass): void
    {
        //
    }
}
