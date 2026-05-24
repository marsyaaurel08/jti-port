<?php

namespace App\Filament\Resources\SelfHealings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SelfHealingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'bni' => 'info',
                        'midtrans' => 'warning',
                        'promo' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('inquiry_count')
                    ->label('Jumlah Inquiry')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('id')
                    ->label('Next Healing')
                    ->formatStateUsing(function ($state, $record) {
                        $backoffMinutes = [
                            1 => 5,
                            2 => 30,
                            3 => 90,
                            4 => 300,
                            5 => 600,
                            6 => 1440,
                        ];

                        // Get latest log dari collection yang sudah di-load
                        $latestLog = $record->selfHealingLogs->sortByDesc('created_at')->first();

                        if (! $latestLog) {
                            // Belum ada attempt, next healing dalam 5 menit dari order created
                            $nextHealingAt = $record->created_at->addMinutes($backoffMinutes[1]);
                        } else {
                            // Ada attempt, cek apakah bisa attempt lagi
                            if ($latestLog->result === 'paid' || $latestLog->attempt >= 6) {
                                return 'Selesai';
                            }

                            $nextAttempt = $latestLog->attempt + 1;
                            $nextHealingAt = $latestLog->created_at->addMinutes($backoffMinutes[$nextAttempt]);
                        }

                        // Hitung relative time
                        $now = now();
                        if ($nextHealingAt <= $now) {
                            return 'Segera';
                        }

                        return $nextHealingAt->diffForHumans($now, ['parts' => 2]);
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
