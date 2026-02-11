<?php

namespace App\Filament\Resources\EnrollmentCourses\Pages;

use App\Filament\Resources\EnrollmentCourses\EnrollmentCourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEnrollmentCourse extends CreateRecord
{
    protected static string $resource = EnrollmentCourseResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Membuat data transaksi manual oleh admin
        $order = \App\Models\Order::create([
            'user_id' => $data['user_id'],
            'invoice_number' => 'INV-ADM-' . strtoupper(\Illuminate\Support\Str::random(8)), // Format: INV-ADM-nomor
            'subtotal' => 0,
            'total_amount' => 0,
            'status' => 'paid',
            'paid_at' => now(), // Tanggal transaksi otomatis hari ini
        ]);

        // Mengaitkan ID Order ke data Enrollment
        $data['order_id'] = $order->id;

        return $data;
    }
    protected function afterCreate(): void
    {
        $enrollment = $this->record; // Data enrollment yang baru saja disimpan

        // Ambil semua ID lesson yang ada di dalam course tersebut
        $lessonIds = \App\Models\Lesson::whereHas('chapter', function ($query) use ($enrollment) {
            $query->where('course_id', $enrollment->course_id);
        })->pluck('id');

        // Persiapkan data untuk bulk insert
        $progressData = $lessonIds->map(function ($lessonId) use ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,
                'lesson_id'     => $lessonId,
                'is_completed'  => false, // Set default ke false sesuai permintaan
                'last_watched_at' => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        })->toArray();

        // Simpan ke tabel course_progress
        \App\Models\CourseProgress::insert($progressData);
    }
}
