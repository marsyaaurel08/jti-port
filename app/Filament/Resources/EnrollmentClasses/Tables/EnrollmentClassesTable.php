<?php

namespace App\Filament\Resources\EnrollmentClasses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EnrollmentClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('class.title')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('order.invoice_number')
                    ->label('Invoice')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('enrolled_at')
                    ->label('Tgl Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->dateTime('d M Y')
                    ->placeholder('Lifetime')
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'gray'),
            ])
            ->filters([
                // TrashedFilter::make(),
                SelectFilter::make('class_id')
                    ->relationship('class', 'title')
                    ->label('Filter Kelas'),
            ])
            ->recordActions([
                ViewAction::make(),
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
