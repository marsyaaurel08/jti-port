<?php

namespace App\Filament\Resources\EnrollmentClasses\Pages;

use App\Filament\Resources\EnrollmentClasses\EnrollmentClassResource;
use App\Http\Controllers\EnrollmentController;
use App\Models\CourseClass;
use App\Models\Enrollment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEnrollmentClass extends CreateRecord
{
    protected static string $resource = EnrollmentClassResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        // Jangan simpan data di sini karena kita akan handle bulk creation
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $classId = $data['class_id'] ?? null;
        $students = $data['students'] ?? [];

        if (! $classId || empty($students)) {
            Notification::make()
                ->title('Error')
                ->body('Kelas dan minimal 1 siswa harus dipilih')
                ->danger()
                ->send();
            throw new \Exception('Kelas dan siswa harus dipilih');
        }

        // Validasi kapasitas kelas
        $class = CourseClass::findOrFail($classId);
        $currentEnrolled = $class->current_enrolled ?? 0;
        $availableSlots = $class->max_capacity - $currentEnrolled;
        $studentCount = count($students);

        if ($studentCount > $availableSlots) {
            Notification::make()
                ->title('Kapasitas Penuh')
                ->body("Hanya tersedia {$availableSlots} slot, namun anda mencoba mendaftarkan {$studentCount} siswa.")
                ->danger()
                ->send();
            throw new \Exception("Kapasitas kelas tidak cukup. Tersedia {$availableSlots} slot.");
        }

        // Cek duplicate user dalam form repeater
        $userIds = array_column($students, 'user_id');
        if (count($userIds) !== count(array_unique($userIds))) {
            Notification::make()
                ->title('Data Duplikat')
                ->body('Siswa tidak boleh didaftarkan lebih dari sekali dalam satu form.')
                ->danger()
                ->send();
            throw new \Exception('Ada siswa yang terdaftar lebih dari sekali.');
        }

        // Cek apakah siswa sudah terdaftar di kelas ini
        $alreadyEnrolled = Enrollment::whereIn('user_id', $userIds)
            ->where('class_id', $classId)
            ->pluck('user_id')
            ->toArray();

        if (! empty($alreadyEnrolled)) {
            $enrolledCount = count($alreadyEnrolled);
            Notification::make()
                ->title('Siswa Sudah Terdaftar')
                ->body("{$enrolledCount} siswa sudah terdaftar di kelas ini.")
                ->warning()
                ->send();
            throw new \Exception('Ada siswa yang sudah terdaftar di kelas ini.');
        }

        // Bulk create enrollments
        $enrollments = [];
        $courseId = $class->course_id;

        foreach ($students as $student) {
            $enrollments[] = [
                'user_id' => $student['user_id'],
                'class_id' => $classId,
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'expires_at' => $student['expires_at'] ?? null,
                'is_completed' => false,
                'completed_lesson_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all enrollments
        Enrollment::insert($enrollments);

        // Update current_enrolled di class
        $class->increment('current_enrolled', count($enrollments));

        // Create enrollment order untuk setiap enrollment
        foreach ($students as $student) {
            $enrollment = Enrollment::where('user_id', $student['user_id'])
                ->where('class_id', $classId)
                ->latest()
                ->first();

            if ($enrollment) {
                EnrollmentController::createEnrollmentOrder($enrollment, $courseId);
            }
        }

        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Sukses')
            ->body("{$studentCount} siswa berhasil didaftarkan ke kelas.")
            ->success()
            ->send();

        // Return enrollment terakhir sebagai record
        return Enrollment::where('class_id', $classId)
            ->latest()
            ->first();
    }
}
