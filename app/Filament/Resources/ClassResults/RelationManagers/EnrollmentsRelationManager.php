<?php

namespace App\Filament\Resources\ClassResults\RelationManagers;

use App\Filament\Resources\ClassResults\ClassResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';
    protected static ?string $title = 'Daftar Mahasiswa & Progres';
    protected static ?string $relatedResource = ClassResultResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Mahasiswa')
                    ->searchable(),

                // Menampilkan progres berdasarkan jumlah materi selesai
                TextColumn::make('actual_completed_lesson_count')
                    ->label('Materi Selesai')
                    ->suffix(fn($record) => " / " . ($record->course->total_lessons ?? 0)),

                // Visualisasi Progres bar (Asumsi total_lessons ada di model Course)
                TextColumn::make('progress_percentage')
                    ->label('Progres (%)')
                    ->state(function ($record) {
                        $total = $record->course->total_lessons ?? 1;
                        $done = $record->actual_completed_lesson_count ?? 0;
                        return number_format(($done / $total) * 100, 1) . '%';
                    })
                    ->badge()
                    ->color(fn($state) => floatval($state) >= 100 ? 'success' : 'warning'),

                TextColumn::make('completed_at')
                    ->label('Tgl Lulus')
                    ->dateTime()
                    ->placeholder('Masih Belajar'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
