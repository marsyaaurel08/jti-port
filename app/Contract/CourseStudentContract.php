<?php

namespace App\Contract;

use App\Models\Enrollment;

interface CourseStudentContract
{
    public function getStudentCourses(int $userId, ?string $filter = null): \Illuminate\Support\Collection;

    public function getStudentCoursesWithTrashed(int $userId, ?string $filter = null): \Illuminate\Support\Collection;

    public function getCourseDetails(Enrollment $enrollment): array;

    public function getCourseDetailsWithTrashed(Enrollment $enrollment): array;

    public function calculateProgress(Enrollment $enrollment): int;

    public function getStatus(Enrollment $enrollment): string;
}
