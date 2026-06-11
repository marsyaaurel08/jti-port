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
                Section::make()
                    ->schema([

                        // ==========================================
                        // 1. HEADER: Gambar, Info Utama, & Harga
                        // ==========================================
                        Grid::make(12)
                            ->schema([
                                ImageEntry::make('thumbnail_url')
                                    ->label('Thumbnail')
                                    ->disk('public')
                                    ->hiddenLabel()
                                    ->circular()
                                    ->columnSpan(['default' => 12, 'sm' => 3, 'md' => 2]),

                                Group::make([
                                    TextEntry::make('title')
                                        ->hiddenLabel()
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large),

                                    TextEntry::make('category.name')
                                        ->hiddenLabel()
                                        ->badge()
                                        ->color('gray')
                                        ->icon('heroicon-m-tag'), // Ikon Tag
                                ])->columnSpan(['default' => 12, 'sm' => 9, 'md' => 7]),

                                TextEntry::make('price')
                                    ->hiddenLabel()
                                    ->money('idr')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::ExtraBold)
                                    ->color('primary')
                                    ->alignEnd()
                                    ->icon('heroicon-m-banknotes') // Ikon Uang/Bank
                                    ->columnSpan(['default' => 12, 'md' => 3]),
                            ]),
                        TextEntry::make('separator2')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'border-t border-gray-200 dark:border-white/10 my-4']),

                        // ==========================================
                        // 3. KONTEN: Deskripsi Panjang
                        // ==========================================
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->prose()
                            ->columnSpanFull(),

                        TextEntry::make('separator1')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'border-t border-gray-200 dark:border-white/10 my-4']),

                        // ==========================================
                        // 2. METRIK & TEKNIS: Disejajarkan ke samping
                        // ==========================================
                        Grid::make(['default' => 1, 'sm' => 3])
                            ->schema([
                                // Kolom 1: Overview
                                Group::make([
                                    TextEntry::make('instructor.name')
                                        ->label('Instruktur')
                                        ->icon('heroicon-m-academic-cap'), // Ikon Topi Wisuda

                                    TextEntry::make('level')
                                        ->label('Kesulitan')
                                        ->badge()
                                        ->color('primary')
                                        ->icon('heroicon-m-chart-bar'), // Ikon Grafik Tingkatan

                                    IconEntry::make('is_active')
                                        ->label('Publikasi')
                                        ->boolean(), // Otomatis menggunakan ikon Check/Cross
                                ]),

                                // Kolom 2: Kuota
                                Group::make([
                                    TextEntry::make('current_enrolled')
                                        ->label('Terisi')
                                        ->suffix(' Siswa')
                                        ->icon('heroicon-m-users'), // Ikon Grup Pengguna

                                    TextEntry::make('max_capacity')
                                        ->label('Total Kuota')
                                        ->icon('heroicon-m-inbox-stack'), // Ikon Tumpukan Kapasitas

                                    TextEntry::make('available_slots')
                                        ->label('Sisa Kuota')
                                        ->color('success')
                                        ->weight(FontWeight::Bold)
                                        ->icon('heroicon-m-ticket') // Ikon Tiket/Slot
                                        ->state(fn($record) => $record->available_slots()),
                                ]),

                                // Kolom 3: Jadwal & Akses
                                Group::make([
                                    TextEntry::make('start_date')
                                        ->label('Dimulai')
                                        ->dateTime()
                                        ->icon('heroicon-m-play-circle'), // Ikon Play

                                    TextEntry::make('end_date')
                                        ->label('Berakhir')
                                        ->dateTime()
                                        ->icon('heroicon-m-stop-circle'), // Ikon Stop

                                    TextEntry::make('meeting_link')
                                        ->label('Link Zoom/Meet')
                                        ->icon('heroicon-m-video-camera') // Ikon Kamera Video (Zoom/Meet)
                                        ->url(fn($state) => $state)
                                        ->color('primary')
                                        ->openUrlInNewTab(),
                                ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
