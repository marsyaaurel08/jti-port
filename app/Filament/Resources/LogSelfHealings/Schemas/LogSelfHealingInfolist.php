<?php

namespace App\Filament\Resources\LogSelfHealings\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class LogSelfHealingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->schema([
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->weight(FontWeight::Bold)
                            ->color('primary'),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('invoice_number')->copyable(),
                                TextEntry::make('order_type')->badge(),
                                TextEntry::make('user.email')->label('User Email'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Tabs::make('Detail Teknis')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Perubahan Status')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('status_before')->badge()->color('danger'),
                                        TextEntry::make('status_after')->badge()->color('success'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Raw Payload')
                            ->schema([
                                KeyValueEntry::make('payload')
                                    ->label('API Response Data')
                                    ->keyLabel('Properti')
                                    ->valueLabel('Nilai'),
                            ]),
                    ]),
            ]);
    }
}
