<?php

namespace App\Filament\Resources\Classes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->circular(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->slug),

                TextColumn::make('category.name')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->sortable(),

                TextColumn::make('level')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'beginner' => 'success',
                        'intermediate' => 'warning',
                        'advanced' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('price')
                    ->money('idr') // Sesuaikan kode mata uang jika bukan Rupiah
                    ->sortable(),

                TextColumn::make('capacity')
                    ->label('Enrolled/Max')
                    ->state(function ($record): string {
                        return "{$record->current_enrolled} / {$record->max_capacity}";
                    })
                    ->description(fn($record) => "Available: " . $record->available_slots()),

                TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
