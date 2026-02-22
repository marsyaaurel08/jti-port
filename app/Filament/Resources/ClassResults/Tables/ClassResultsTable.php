<?php

namespace App\Filament\Resources\ClassResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClassResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('instructor.name')
                    ->label('Instruktur'),

                TextColumn::make('enrollments_count')
                    ->counts('enrollments')
                    ->label('Total Mahasiswa')
                    ->alignCenter(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Lihat Detail Nilai'),
            ]);
    }
}
