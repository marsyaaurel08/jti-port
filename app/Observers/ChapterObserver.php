<?php

namespace App\Observers;

use App\Models\Chapter;

class ChapterObserver
{
    /**
     * Handle the Chapter "deleted" event.
     * Update the parent course's total_lessons after chapter deletion.
     */
    public function deleted(Chapter $chapter): void
    {
        if ($chapter->course) {
            $course = $chapter->course;
            $totalLessons = $course->lessons()->count();

            if ($course->total_lessons !== $totalLessons) {
                $course->total_lessons = $totalLessons;
                $course->saveQuietly(); // Save without triggering the observer again
            }
        }
    }
}
