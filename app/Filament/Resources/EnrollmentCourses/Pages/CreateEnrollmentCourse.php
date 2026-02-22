<?php

namespace App\Filament\Resources\EnrollmentCourses\Pages;

use App\Filament\Resources\EnrollmentCourses\EnrollmentCourseResource;
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
            $this->createOrderAndProgress($this->record, $courseId);
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

            $this->createOrderAndProgress($enrollment, $courseId);
        }

        // Update record pertama dengan siswa pertama
        $firstRecord->update([
            'user_id' => $studentsData[0]['user_id'],
            'course_id' => $courseId,
        ]);

        $this->createOrderAndProgress($firstRecord, $courseId);

        session()->forget(['bulk_students', 'bulk_course_id']);
    }

    private function createOrderAndProgress($enrollment, $courseId): void
    {
        // Membuat data transaksi manual oleh admin
        $order = \App\Models\Order::create([
            'user_id' => $enrollment->user_id,
            'invoice_number' => 'INV-ADM-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'subtotal' => 0,
            'total_amount' => 0,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Asosiasikan Order ke Enrollment
        $enrollment->update(['order_id' => $order->id]);

        // Ambil semua ID lesson dari course
        $lessonIds = \App\Models\Lesson::whereHas('chapter', function ($query) use ($enrollment) {
            $query->where('course_id', $enrollment->course_id);
        })->pluck('id');

        // Persiapkan data untuk bulk insert
        $progressData = $lessonIds->map(function ($lessonId) use ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lessonId,
                'is_completed' => false,
                'last_watched_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        // Simpan ke tabel course_progress
        if (! empty($progressData)) {
            \App\Models\CourseProgress::insert($progressData);
        }
    }
}
