<?php

namespace App\Services;

use App\Contract\CourseStudentContract;
use App\Models\Enrollment;

class CourseStudentService implements CourseStudentContract
{
    public function getStudentCourses(int $userId, ?string $filter = null): \Illuminate\Support\Collection
    {
        // Get user's enrollments with course, and progress data
        $enrollments = Enrollment::with(['course', 'progress'])
            ->where('user_id', $userId)
            ->get();

        // Transform enrollments to course data with progress
        $courses = $enrollments->map(function ($enrollment) {
            return $this->getCourseDetails($enrollment);
        });

        // Filter by status if requested
        if ($filter !== null && $filter !== 'all') {
            $courses = $courses->where('status', $filter);
        }

        return $courses->values();
    }

    public function getCourseDetails(Enrollment $enrollment): array
    {
        $course = $enrollment->course;

        // Handle case where course might be null (deleted course but enrollment still exists)
        if ($course === null) {
            return [
                'id' => null,
                'title' => 'Course Not Available',
                'description' => 'This course is no longer available.',
                'image' => 'https://picsum.photos/400/200',
                'progress' => 0,
                'status' => 'unavailable',
                'enrollment' => $enrollment,
                'slug' => null,
                'first_lesson_slug' => null,
                'last_accessed_lesson_slug' => null,
            ];
        }

        // Get first lesson slug for navigation
        $firstLessonSlug = null;
        $firstChapter = $course->chapters()->with('lessons')->orderBy('sort_order')->first();
        if ($firstChapter && $firstChapter->lessons->isNotEmpty()) {
            $firstLesson = $firstChapter->lessons->sortBy('sort_order')->first();
            $firstLessonSlug = $firstLesson->slug ?? null;
        }

        // Get last accessed lesson slug or fall back to first lesson
        $lastAccessedLessonSlug = null;
        if ($enrollment->last_accessed_lesson_id) {
            $lastAccessedLesson = \App\Models\Lesson::find($enrollment->last_accessed_lesson_id);
            $lastAccessedLessonSlug = $lastAccessedLesson->slug ?? $firstLessonSlug;
        } else {
            $lastAccessedLessonSlug = $firstLessonSlug;
        }

        return [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'image' => $course->thumbnail_url ?? 'https://picsum.photos/400/200',
            'progress' => $this->calculateProgress($enrollment),
            'status' => $this->getStatus($enrollment),
            'enrollment' => $enrollment,
            'slug' => $course->slug,
            'first_lesson_slug' => $firstLessonSlug,
            'last_accessed_lesson_slug' => $lastAccessedLessonSlug,
        ];
    }

    public function calculateProgress(Enrollment $enrollment): int
    {
        $course = $enrollment->course;

        // Handle case where course might be null
        if ($course === null) {
            return 0;
        }

        // Calculate progress based on completed lessons vs total lessons
        $totalLessons = $course->total_lessons ?? 0;
        $completedLessons = $enrollment->completed_lesson_count ?? 0;

        return $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
    }

    public function getStatus(Enrollment $enrollment): string
    {
        return $enrollment->is_completed ? 'completed' : 'in-progress';
    }
}
