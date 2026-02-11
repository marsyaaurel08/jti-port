<?php

namespace App\Filament\Resources\EnrollmentCourses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
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

                TextColumn::make('completed_lesson_count')
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

                TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->dateTime('d M Y')
                    ->placeholder('Lifetime')
                    ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'gray'),
            ])
            ->filters([
                // TrashedFilter::make(),
                SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Filter Kursus'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
