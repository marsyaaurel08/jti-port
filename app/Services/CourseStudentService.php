<?php

namespace App\Services;

use App\Contract\CourseStudentContract;
use App\Models\Enrollment;

class CourseStudentService implements CourseStudentContract
{
    public function getStudentCourses(int $userId, ?string $filter = null): \Illuminate\Support\Collection
    {
        // Get user's enrollments with course, chapters, lessons, and progress data
        $enrollments = Enrollment::with([
            'course' => function ($query) {
                $query->with(['chapters' => function ($q) {
                    $q->orderBy('sort_order')->with(['lessons' => function ($lq) {
                        $lq->orderBy('sort_order');
                    }]);
                }]);
            },
            'lastAccessedLesson',
            'progress',
        ])
            ->where('user_id', $userId)
            ->whereNull('class_id')
            ->latest()
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

    public function getStudentCoursesWithTrashed(int $userId, ?string $filter = null): \Illuminate\Support\Collection
    {
        // Get user's enrollments with course (including trashed), chapters, lessons, and progress data
        $enrollments = Enrollment::with([
            'course' => function ($query) {
                $query->withTrashed()->with(['chapters' => function ($q) {
                    $q->orderBy('sort_order')->with(['lessons' => function ($lq) {
                        $lq->orderBy('sort_order');
                    }]);
                }]);
            },
            'lastAccessedLesson',
            'progress',
        ])
            ->where('user_id', $userId)
            ->whereNull('class_id')
            ->latest()
            ->get();

        // Transform enrollments to course data with progress
        $courses = $enrollments->map(function ($enrollment) {
            return $this->getCourseDetailsWithTrashed($enrollment);
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

        // Get first lesson slug for navigation (use eager loaded collection if available)
        $firstLessonSlug = null;
        if ($course->relationLoaded('chapters')) {
            $firstChapter = $course->chapters->sortBy('sort_order')->first();
            if ($firstChapter && $firstChapter->relationLoaded('lessons') && $firstChapter->lessons->isNotEmpty()) {
                $firstLesson = $firstChapter->lessons->sortBy('sort_order')->first();
                $firstLessonSlug = $firstLesson->slug ?? null;
            }
        } else {
            $firstChapter = $course->chapters()->with('lessons')->orderBy('sort_order')->first();
            if ($firstChapter && $firstChapter->lessons->isNotEmpty()) {
                $firstLesson = $firstChapter->lessons->sortBy('sort_order')->first();
                $firstLessonSlug = $firstLesson->slug ?? null;
            }
        }

        // Get last accessed lesson slug or fall back to first lesson
        $lastAccessedLessonSlug = null;
        if ($enrollment->last_accessed_lesson_id) {
            if ($enrollment->relationLoaded('lastAccessedLesson') && $enrollment->lastAccessedLesson) {
                $lastAccessedLessonSlug = $enrollment->lastAccessedLesson->slug ?? $firstLessonSlug;
            } else {
                $lastAccessedLesson = \App\Models\Lesson::find($enrollment->last_accessed_lesson_id);
                $lastAccessedLessonSlug = $lastAccessedLesson->slug ?? $firstLessonSlug;
            }
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

    public function getCourseDetailsWithTrashed(Enrollment $enrollment): array
    {
        $course = $enrollment->course;

        // Handle case where course might be null (permanently deleted course but enrollment still exists)
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
                'is_trashed' => true,
            ];
        }

        // Get first lesson slug for navigation (use eager loaded collection if available)
        $firstLessonSlug = null;
        if ($course->relationLoaded('chapters')) {
            $firstChapter = $course->chapters->sortBy('sort_order')->first();
            if ($firstChapter && $firstChapter->relationLoaded('lessons') && $firstChapter->lessons->isNotEmpty()) {
                $firstLesson = $firstChapter->lessons->sortBy('sort_order')->first();
                $firstLessonSlug = $firstLesson->slug ?? null;
            }
        } else {
            $firstChapter = $course->chapters()->with('lessons')->orderBy('sort_order')->first();
            if ($firstChapter && $firstChapter->lessons->isNotEmpty()) {
                $firstLesson = $firstChapter->lessons->sortBy('sort_order')->first();
                $firstLessonSlug = $firstLesson->slug ?? null;
            }
        }

        // Get last accessed lesson slug or fall back to first lesson
        $lastAccessedLessonSlug = null;
        if ($enrollment->last_accessed_lesson_id) {
            if ($enrollment->relationLoaded('lastAccessedLesson') && $enrollment->lastAccessedLesson) {
                $lastAccessedLessonSlug = $enrollment->lastAccessedLesson->slug ?? $firstLessonSlug;
            } else {
                $lastAccessedLesson = \App\Models\Lesson::find($enrollment->last_accessed_lesson_id);
                $lastAccessedLessonSlug = $lastAccessedLesson->slug ?? $firstLessonSlug;
            }
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
            'is_trashed' => $course->trashed(),
        ];
    }

    public function calculateProgress(Enrollment $enrollment): int
    {
        $course = $enrollment->course;

        // Handle case where course might be null
        if ($course === null) {
            return 0;
        }

        // Using actual_completed_lesson_count which counts from CourseProgress records (single source of truth)
        $totalLessons = $course->total_lessons ?? 0;
        $completedLessons = $enrollment->actual_completed_lesson_count ?? 0;

        return $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
    }

    public function getStatus(Enrollment $enrollment): string
    {
        return $enrollment->is_completed ? 'completed' : 'in-progress';
    }
}
