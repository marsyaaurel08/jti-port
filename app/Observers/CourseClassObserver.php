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
        $this->clearCache($courseClass);
    }

    /**
     * Handle the CourseClass "updated" event.
     */
    public function updated(CourseClass $courseClass): void
    {
        $this->clearCache($courseClass);
    }

    /**
     * Handle the CourseClass "deleted" event.
     */
    public function deleted(CourseClass $courseClass): void
    {
        $this->clearCache($courseClass);
    }

    /**
     * Handle the CourseClass "restored" event.
     */
    public function restored(CourseClass $courseClass): void
    {
        $this->clearCache($courseClass);
    }

    /**
     * Handle the CourseClass "force deleted" event.
     */
    public function forceDeleted(CourseClass $courseClass): void
    {
        $this->clearCache($courseClass);
    }

    /**
     * Invalidate all course class related caches
     */
    private function clearCache(CourseClass $courseClass): void
    {
        Cache::forget("class_checkout:{$courseClass->slug}");
        Cache::forget("class_detail:{$courseClass->slug}");
        Cache::forget("classes_slider_all");
        
        $version = Cache::get('classes_catalog_version', 1);
        Cache::put('classes_catalog_version', $version + 1);
    }
}
