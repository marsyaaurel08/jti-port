<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;

class CourseObserver
{
    public function created(Course $course): void
    {
        $this->clearCache($course);
    }

    public function updated(Course $course): void
    {
        $this->clearCache($course);
    }

    private function clearCache(Course $course): void
    {
        Cache::forget("course_checkout:{$course->slug}");
        Cache::forget("course_detail:{$course->slug}");
        Cache::forget("course_slider_all");
        
        $version = Cache::get('courses_catalog_version', 1);
        Cache::put('courses_catalog_version', $version + 1);
    }
    /**
     * Handle the Course "saved" event.
     * Automatically calculate total_lessons based on the number of lessons.
     */
    public function saved(Course $course): void
    {
        $totalLessons = $course->lessons()->count();

        // Only update if the value has changed to avoid unnecessary database queries
        if ($course->total_lessons !== $totalLessons) {
            $course->total_lessons = $totalLessons;
            $course->saveQuietly(); // Save without triggering the observer again
        }
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        $this->clearCache($course);
        // Lessons will be cascade deleted by database foreign key
        // No additional action needed
    }

    public function restored(Course $course): void
    {
        $this->clearCache($course);
    }

    public function forceDeleted(Course $course): void
    {
        $this->clearCache($course);
    }
}
