<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->extraImgAttributes(['class' => 'w-full h-64 object-cover rounded-lg mb-3']),

                TextColumn::make('title')
                    ->label('Judul Kursus')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),
                // TextColumn::make('description')
                //     ->label('Deskripsi')
                //     ->limit(100)
                //     ->wrap(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                // TextColumn::make('level')
                //     ->label('Level')
                //     ->badge()
                //     ->color(fn(string $state): string => match ($state) {
                //         'beginner' => 'success',
                //         'intermediate' => 'warning',
                //         'advanced' => 'danger',
                //     }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'warning',
                    }),
                // TextColumn::make('total_duration')
                //     ->label('Durasi')
                //     ->formatStateUsing(fn(int $state): string => $state . 'm')
                //     ->sortable(),
                // TextColumn::make('total_lessons')
                //     ->label('Pelajaran')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('avg_rating')
                //     ->label('Rating')
                //     ->formatStateUsing(fn(float $state): string => number_format($state, 1))
                //     ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->after(function ($record) {
                        \App\Models\CourseClass::where('course_id', $record->id)->delete();
                    }),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                \App\Models\CourseClass::where('course_id', $record->id)->delete();
                            }
                        }),
                    ForceDeleteBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                \App\Models\CourseClass::where('course_id', $record->id)->forceDelete();
                            }
                        }),
                    RestoreBulkAction::make()
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                \App\Models\CourseClass::withTrashed()->where('course_id', $record->id)->restore();
                            }
                        }),
                ]),
            ]);
    }
}
