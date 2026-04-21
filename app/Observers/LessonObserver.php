<?php

namespace App\Observers;

use App\Models\Lesson;

class LessonObserver
{
    /**
     * Handle the Lesson "created" event.
     * Update the parent course's total_lessons.
     */
    public function created(Lesson $lesson): void
    {
        $this->updateCourseTotalLessons($lesson);
    }

    /**
     * Handle the Lesson "updated" event.
     * Update the parent course's total_lessons.
     */
    public function updated(Lesson $lesson): void
    {
        $this->updateCourseTotalLessons($lesson);
    }

    /**
     * Handle the Lesson "deleted" event.
     * Update the parent course's total_lessons.
     */
    public function deleted(Lesson $lesson): void
    {
        $this->updateCourseTotalLessons($lesson);
    }

    /**
     * Update the parent course's total_lessons count.
     */
    protected function updateCourseTotalLessons(Lesson $lesson): void
    {
        if ($lesson->chapter && $lesson->chapter->course) {
            $course = $lesson->chapter->course;
            $totalLessons = $course->lessons()->count();

            if ($course->total_lessons !== $totalLessons) {
                $course->total_lessons = $totalLessons;
                $course->saveQuietly(); // Save without triggering the observer again
            }
        }
    }
}
