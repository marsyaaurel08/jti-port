<?php

namespace App\Services;

use App\Contract\CourseStudentContract;
use App\Models\Enrollment;

class CourseStudentService implements CourseStudentContract
{

    public function getStudentCourses(int $userId, ?string $filter = null): \Illuminate\Support\Collection
    {
        // Get user's enrollments with course, class, and progress data
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

        return [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'image' => $course->thumbnail_url ?? 'https://picsum.photos/400/200',
            'progress' => $this->calculateProgress($enrollment),
            'status' => $this->getStatus($enrollment),
            'enrollment' => $enrollment,
        ];
    }

    public function calculateProgress(Enrollment $enrollment): int
    {
        $course = $enrollment->course;

        // Calculate progress based on completed lessons vs total lessons
        $totalLessons = $course->total_lessons ?? 0;
        $completedLessons = $enrollment->completed_lesson_count ?? 0;

        return $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100): 0;
    }


    public function getStatus(Enrollment $enrollment): string
    {
        return $enrollment->is_completed ? 'completed' : 'in-progress';
    }
}
