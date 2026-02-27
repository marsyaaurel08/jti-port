<?php

namespace App\Filament\Resources\EnrollmentCourses\Pages;

use App\Filament\Resources\EnrollmentCourses\EnrollmentCourseResource;
use App\Http\Controllers\EnrollmentController;
use Filament\Resources\Pages\CreateRecord;

class CreateEnrollmentCourse extends CreateRecord
{
    protected static string $resource = EnrollmentCourseResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ekstrak students array dan course_id
        $studentsData = $data['students'] ?? [];
        $courseId = $data['course_id'] ?? null;

        // Simpan ke session untuk digunakan di afterCreate
        session([
            'bulk_students' => $studentsData,
            'bulk_course_id' => $courseId,
        ]);

        // Return data hanya untuk record pertama (akan di-override di afterCreate)
        if (! empty($studentsData)) {
            return [
                'user_id' => $studentsData[0]['user_id'] ?? null,
                'course_id' => $courseId,
                'enrolled_at' => $data['enrolled_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'completed_lesson_count' => $data['completed_lesson_count'] ?? 0,
                'is_completed' => $data['is_completed'] ?? false,
            ];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Ambil data dari session
        $studentsData = session('bulk_students', []);
        $courseId = session('bulk_course_id');

        // Jika hanya ada 1 siswa, gunakan record yang sudah dibuat
        if (count($studentsData) === 1) {
            EnrollmentController::createEnrollmentOrder($this->record, $courseId);
            session()->forget(['bulk_students', 'bulk_course_id']);

            return;
        }

        // Untuk multiple siswa, hapus record pertama yang dibuat otomatis
        // kemudian buat record baru untuk setiap siswa
        $firstRecord = $this->record;

        // Buat records untuk siswa ke-2 dan seterusnya
        foreach (array_slice($studentsData, 1) as $studentData) {
            $enrollment = \App\Models\Enrollment::create([
                'user_id' => $studentData['user_id'],
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'expires_at' => null,
                'completed_lesson_count' => 0,
                'is_completed' => false,
            ]);

            EnrollmentController::createEnrollmentOrder($enrollment, $courseId);
        }

        // Update record pertama dengan siswa pertama
        $firstRecord->update([
            'user_id' => $studentsData[0]['user_id'],
            'course_id' => $courseId,
        ]);

        EnrollmentController::createEnrollmentOrder($firstRecord, $courseId);

        session()->forget(['bulk_students', 'bulk_course_id']);
    }
}
