<?php

namespace App\Contract;

use App\Models\Enrollment;

interface ClassStudentContract
{
    public function getStudentClasses(int $userId, ?string $filter = null): \Illuminate\Support\Collection;

    public function getClassDetails(Enrollment $enrollment): array;

    public function calculateClassProgress(Enrollment $enrollment): int;

    public function getClassStatus(Enrollment $enrollment): string;

    public function isEnrolledInClass(int $userId, int $classId): bool;

    public function canEnrollInClass(int $userId, int $classId): bool;
}
