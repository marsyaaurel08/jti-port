<?php

namespace App\Filament\Resources\Classes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Support\Str;

class ClassesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Header Ringkas: Fokus pada Judul & Harga
                Grid::make(4)
                    ->schema([
                        ImageEntry::make('thumbnail_url')
                            ->hiddenLabel()
                            ->circular()
                            ->columnSpan(1),

                        Group::make([
                            TextEntry::make('title')
                                ->hiddenLabel()
                                ->weight(FontWeight::Bold)
                                ->size(TextSize::Large),

                            TextEntry::make('category.name')
                                ->hiddenLabel()
                                ->badge()
                                ->color('gray')
                                ->icon('heroicon-m-tag'),
                        ])->columnSpan(2),

                        TextEntry::make('price')
                            ->hiddenLabel()
                            ->money('idr')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::ExtraBold)
                            ->alignEnd()
                            ->columnSpan(1),
                    ]),

                // Menggunakan Tabs untuk memisahkan konteks agar tidak menumpuk
                Tabs::make('Detail Kelas')
                    ->tabs([
                        // Tab 1: Ringkasan Status & Peserta
                        Tab::make('Overview')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('instructor.name')
                                            ->label('Instructor')
                                            ->icon('heroicon-m-user-circle'),

                                        TextEntry::make('level')
                                            ->label('Difficulty')
                                            ->badge()
                                            ->color('primary'),

                                        IconEntry::make('is_active')
                                            ->label('Published')
                                            ->boolean(),
                                    ]),

                                // Statistik Kapasitas yang lebih visual
                                Section::make('Ketersediaan Kursi')
                                    ->compact()
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('current_enrolled')
                                                    ->label('Terisi')
                                                    ->suffix(' Siswa'),
                                                TextEntry::make('max_capacity')
                                                    ->label('Total Kuota'),
                                                TextEntry::make('available_slots')
                                                    ->label('Sisa')
                                                    ->color('success')
                                                    ->weight(FontWeight::Bold)
                                                    ->state(fn($record) => $record->available_slots()),
                                            ]),
                                    ]),
                            ]),

                        // Tab 2: Jadwal & Link (Hanya yang teknis)
                        Tab::make('Schedule')
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('start_date')
                                            ->dateTime()
                                            ->icon('heroicon-m-play-circle'),
                                        TextEntry::make('end_date')
                                            ->dateTime()
                                            ->icon('heroicon-m-stop-circle'),
                                        TextEntry::make('meeting_link')
                                            ->label('Link Akses')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->url(fn($state) => $state)
                                            ->color('primary')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // Tab 3: Konten/Deskripsi
                        Tab::make('Description')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->html()
                                    ->prose(), // Membuat tipografi teks panjang lebih cantik
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
