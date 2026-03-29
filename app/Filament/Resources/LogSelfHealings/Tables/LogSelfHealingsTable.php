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
                    ->label('Order Type')
                    ->translateLabel()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'course' => 'info',
                        'class' => 'warning',
                    }),

                TextColumn::make('status_before')
                    ->label('Before')
                    ->badge()
                    ->color('danger'),

                IconColumn::make('arrow')
                    ->icon('heroicon-m-arrow-right')
                    ->label(''),

                TextColumn::make('status_after')
                    ->label('After')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('order_type')
                    ->options([
                        'course' => 'Course',
                        'class' => 'Class',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
