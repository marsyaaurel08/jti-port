<?php

namespace App\Filament\Resources\LogSelfHealings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LogSelfHealingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Waktu Kejadian')
                                    ->dateTime(),
                                TextEntry::make('order.id')
                                    ->label('Order ID'),
                                TextEntry::make('invoice_number')
                                    ->label('Invoice')
                                    ->copyable(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('attempt')
                                    ->label('Percobaan ke'),
                                TextEntry::make('result')
                                    ->label('Hasil')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'pending' => 'info',
                                        'error' => 'danger',
                                        'skipped' => 'gray',
                                        default => 'gray',
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Informasi Error')
                    ->schema([
                        TextEntry::make('error_message')
                            ->label('Pesan Error')
                            ->columnSpanFull()
                            ->visible(fn($record) => $record->error_message !== null),
                    ])
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->error_message !== null),

                Section::make('Raw Response')
                    ->schema([
                        TextEntry::make('raw_response')
                            ->label('Gateway Response')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-')
                            ->copyable(),
                    ])
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->raw_response !== null),
            ]);
    }
}
