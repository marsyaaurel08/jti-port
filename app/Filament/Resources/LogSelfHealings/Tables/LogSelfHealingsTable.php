<?php

namespace App\Filament\Resources\LogSelfHealings\Tables;

use App\Models\LogSelfHealing;
use Filament\Tables\Columns\IconColumn;
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

                TextColumn::make('user.name')
                    ->searchable()
                    ->description(fn(LogSelfHealing $record): string => $record->invoice_number),

                TextColumn::make('order_type')
                    ->label('Jenis Order')
                    ->translateLabel()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'course' => 'info',
                        'class' => 'warning',
                    }),

                // TextColumn::make('status_before')
                //     ->label('Sebelum')
                //     ->badge()
                //     ->color('danger')
                //     ->tooltip(fn(LogSelfHealing $record): string => "Status sebelumnya: {$record->status_before}"),

                // IconColumn::make('arrow')
                //     ->icon('heroicon-m-arrow-right')
                //     ->default('heroicon-m-minus')
                //     ->label(''),

                // TextColumn::make('status_after')
                //     ->label('Sesudah')
                //     ->badge()
                //     ->color('success')
                //     ->tooltip(fn(LogSelfHealing $record): string => "Status sesudahnya: {$record->status_after}"),
            ])
            ->filters([
                SelectFilter::make('order_type')
                    ->label('Jenis Order')
                    ->translateLabel()
                    ->searchable()
                    ->options([
                        'course' => 'Course',
                        'class' => 'Class',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
