<?php

namespace App\Filament\Resources\EnrollmentClasses\Pages;

use App\Filament\Resources\EnrollmentClasses\EnrollmentClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEnrollmentClass extends CreateRecord
{
    protected static string $resource = EnrollmentClassResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Membuat transaksi otomatis di tabel orders
        $order = \App\Models\Order::create([
            'user_id' => $data['user_id'],
            'invoice_number' => 'INV-ADM-' . now()->format('Ymd') . strtoupper(\Illuminate\Support\Str::random(5)), // Format INV-ADM-nomor
            'subtotal' => 0,
            'total_amount' => 0,
            'status' => 'paid',
            'paid_at' => now(), // Tanggal transaksi otomatis
        ]);

        // Hubungkan enrollment dengan order yang baru dibuat
        $data['order_id'] = $order->id;
        $data['enrolled_at'] = $data['enrolled_at'] ?? now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $enrollment = $this->record;

        // Ambil course_id dari relasi class_id
        $courseId = $enrollment->class?->course_id;

        if ($courseId) {
            $lessonIds = \App\Models\Lesson::whereHas('chapter', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })->pluck('id');

            $progressData = $lessonIds->map(function ($lessonId) use ($enrollment) {
                return [
                    'enrollment_id' => $enrollment->id,
                    'lesson_id'     => $lessonId,
                    'is_completed'  => false,
                    'last_watched_at' => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            })->toArray();

            \App\Models\CourseProgress::insert($progressData);
        }
    }
}
