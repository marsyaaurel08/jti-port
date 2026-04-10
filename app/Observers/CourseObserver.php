<?php

namespace App\Observers;

use App\Models\Course;

class CourseObserver
{
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
        // Lessons will be cascade deleted by database foreign key
        // No additional action needed
    }
}
