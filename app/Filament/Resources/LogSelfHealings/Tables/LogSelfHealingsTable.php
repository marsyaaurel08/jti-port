<?php

namespace App\Filament\Resources\LogSelfHealings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogSelfHealingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Waktu Kejadian'),

                TextColumn::make('order.id')
                    ->label('Order ID')
                    ->searchable(),

                TextColumn::make('gateway')
                    ->label('Gateway')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bni' => 'info',
                        'midtrans' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('action')
                    ->label('Aksi'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'unresolved' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('gateway')
                    ->label('Gateway')
                    ->searchable()
                    ->options([
                        'bni' => 'BNI',
                        'midtrans' => 'Midtrans',
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->searchable()
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'unresolved' => 'Unresolved',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
