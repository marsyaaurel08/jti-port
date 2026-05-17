<?php

namespace App\Filament\Resources\EnrollmentCourses\Tables;

use Filament\Actions\{BulkActionGroup, DeleteAction, DeleteBulkAction, ForceDeleteBulkAction, RestoreBulkAction, ViewAction};
use Filament\Tables\Columns\{IconColumn, TextColumn};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnrollmentCoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order.invoice_number')
                    ->label('Invoice')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('actual_completed_lesson_count')
                    ->label('Materi Selesai')
                    ->alignCenter()
                    ->suffix(' Materi'),

                IconColumn::make('is_completed')
                    ->label('Lulus')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('enrolled_at')
                    ->label('Tgl Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                // TrashedFilter::make(),
                SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Filter Kursus'),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
