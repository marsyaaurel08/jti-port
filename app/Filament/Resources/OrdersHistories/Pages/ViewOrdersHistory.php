<?php

namespace App\Filament\Resources\OrdersHistories\Pages;

use App\Filament\Resources\OrdersHistories\OrdersHistoryResource;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrdersHistory extends ViewRecord
{
    protected static string $resource = OrdersHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getVerifyPaymentAction(),
        ];
    }

    protected function getVerifyPaymentAction(): Action
    {
        return Action::make('verify_payment')
            ->label('Verifikasi Pembayaran')
            ->icon('heroicon-m-check-badge')
            ->color('success')
            ->visible(fn () => $this->record->status === 'pending' && $this->record->payment_proof)
            ->requiresConfirmation()
            ->modalHeading('Verifikasi Pembayaran Manual')
            ->modalDescription('Anda akan memperbarui status pesanan menjadi "Dibayar". Tindakan ini tidak dapat diurungkan.')
            ->modalSubmitActionLabel('Ya, Verifikasi')
            ->action(function () {
                try {
                    $this->record->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    // Register student to enrollments and create course progress
                    $orderItems = $this->record->orderItems;

                    foreach ($orderItems as $item) {
                        $enrollment = Enrollment::updateOrCreate(
                            [
                                'user_id' => $this->record->user_id,
                                'course_id' => $item->course_id,
                                'class_id' => $item->class_id,
                                'order_id' => $this->record->id,
                            ],
                            [
                                'enrolled_at' => now(),
                                'completed_lesson_count' => 0,
                            ]
                        );

                        // Create CourseProgress records for all lessons in the course
                        $course = $item->course;
                        if ($course) {
                            $lessons = $course->lessons()->get();
                            foreach ($lessons as $lesson) {
                                CourseProgress::firstOrCreate(
                                    [
                                        'enrollment_id' => $enrollment->id,
                                        'lesson_id' => $lesson->id,
                                    ],
                                    [
                                        'is_completed' => false,
                                    ]
                                );
                            }
                        }
                    }

                    Notification::make()
                        ->success()
                        ->title('Pembayaran Terverifikasi')
                        ->body('Status pesanan telah diperbarui menjadi "Dibayar". Siswa terdaftar ke enrollment dengan semua lesson progress dicatat.')
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Gagal Memverifikasi')
                        ->body('Terjadi kesalahan: '.$e->getMessage())
                        ->send();
                }
            });
    }
}
