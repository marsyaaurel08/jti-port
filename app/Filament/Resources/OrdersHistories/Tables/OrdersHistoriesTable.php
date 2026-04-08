<?php

namespace App\Filament\Resources\OrdersHistories\Tables;

use App\Models\CourseProgress;
use App\Models\Enrollment;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('payment_proof')
                    ->label('Bukti')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->payment_proof ? 'Ada bukti pembayaran' : 'Belum ada bukti'),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('paid_at')
                    ->label('Dibayar Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye'),

                Action::make('verify_payment')
                    ->label('Verifikasi')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' && $record->payment_proof)
                    ->modalHeading('Verifikasi Pembayaran')
                    ->modalIcon('heroicon-m-check-badge')
                    ->modalSubheading('Periksa bukti pembayaran sebelum verifikasi')
                    ->modalWidth('lg')
                    ->form([
                        // This will be replaced with custom modal content
                    ])
                    ->modalContent(fn ($record) => view('filament.components.verify-payment-modal', [
                        'record' => $record,
                    ]))
                    ->action(function ($record) {
                        try {
                            $record->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                            ]);

                            // Register student to enrollments and create course progress
                            $orderItems = $record->orderItems;

                            foreach ($orderItems as $item) {
                                $enrollment = Enrollment::updateOrCreate(
                                    [
                                        'user_id' => $record->user_id,
                                        'course_id' => $item->course_id,
                                        'class_id' => $item->class_id,
                                        'order_id' => $record->id,
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
                                ->title('Pembayaran Terverifikasi ✓')
                                ->body("Invoice {$record->invoice_number} telah diverifikasi. Siswa terdaftar ke enrollment dengan semua lesson progress dicatat.")
                                ->icon('heroicon-o-check-circle')
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Memverifikasi')
                                ->body('Terjadi kesalahan: '.$e->getMessage())
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Ya, Verifikasi')
                    ->modalCancelActionLabel('Batal'),
            ])
            ->bulkActions([]);
    }
}
