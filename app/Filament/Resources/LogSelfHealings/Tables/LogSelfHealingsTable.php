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

                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(),

                TextColumn::make('attempt')
                    ->label('Percobaan ke')
                    ->sortable(),

                TextColumn::make('result')
                    ->label('Hasil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'info',
                        'error' => 'danger',
                        'skipped' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('error_message')
                    ->label('Pesan Error')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->error_message),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('result')
                    ->label('Hasil')
                    ->searchable()
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'error' => 'Error',
                        'skipped' => 'Skipped',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
