<?php

namespace App\Services;

use App\Contract\ClassStudentContract;
use App\Models\CourseClass;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClassStudentService implements ClassStudentContract
{
    public function getStudentClasses(int $userId, ?string $filter = null): Collection
    {
        $enrollments = Enrollment::with(['class', 'class.course', 'class.instructor'])
            ->where('user_id', $userId)
            ->whereNotNull('class_id')
            ->get();

        $classes = $enrollments->map(function ($enrollment) {
            return $this->getClassDetails($enrollment);
        });

        if ($filter !== null && $filter !== 'all') {
            $classes = $classes->where('status', $filter);
        }

        return $classes->values();
    }

    public function getClassDetails(Enrollment $enrollment): array
    {
        $class = $enrollment->class;
        $course = $class->course;
        $instructor = $class->instructor;

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
            'id' => $class->id,
            'title' => $class->title,
            'course_title' => $course->title,
            'instructor_name' => $instructor->name,
            'instructor_email' => $instructor->email,
            'thumbnail_url' => $class->thumbnail_url,
            'price' => (float) $class->price,
            'slug' => $course->slug,
            'start_date' => $class->start_date?->format('Y-m-d H:i'),
            'end_date' => $class->end_date?->format('Y-m-d H:i'),
            'max_capacity' => $class->max_capacity,
            'current_enrolled' => $class->current_enrolled,
            'meeting_link' => $class->meeting_link,
            'is_active' => $class->is_active,
            'status' => $this->getClassStatus($enrollment),
            'progress' => $this->calculateClassProgress($enrollment),
            'first_lesson_slug' => $firstLessonSlug,
            'last_accessed_lesson_slug' => $lastAccessedLessonSlug,
            'enrolled_at' => $enrollment->enrolled_at?->format('Y-m-d H:i'),
            'expires_at' => $enrollment->expires_at?->format('Y-m-d H:i'),
            'is_completed' => $enrollment->is_completed,
            'completed_at' => $enrollment->completed_at?->format('Y-m-d H:i'),
        ];
        
    }

    public function calculateClassProgress(Enrollment $enrollment): int
    {
        if (! $enrollment->is_completed) {
            return 0;
        }

        return 100;
    }

    public function getClassStatus(Enrollment $enrollment): string
    {
        $class = $enrollment->class;
        $now = Carbon::now();

        if ($enrollment->is_completed) {
            return 'completed';
        }

        if ($class->start_date > $now) {
            return 'upcoming';
        }

        if ($class->end_date < $now) {
            return 'finished';
        }

        return 'ongoing';
    }

    public function isEnrolledInClass(int $userId, int $classId): bool
    {
        return Enrollment::where('user_id', $userId)
            ->where('class_id', $classId)
            ->exists();
    }

    public function canEnrollInClass(int $userId, int $classId): bool
    {
        if ($this->isEnrolledInClass($userId, $classId)) {
            return false;
        }

        $class = CourseClass::find($classId);

        if (! $class || ! $class->is_active) {
            return false;
        }

        if ($class->current_enrolled >= $class->max_capacity) {
            return false;
        }

        return true;
    }
}
