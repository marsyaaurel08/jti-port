<?php

namespace App\Filament\Resources\ClassResults\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassResultInfolist
{

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ===== SECTION 1: INFORMASI UTAMA KELAS =====
                Section::make('📚 Informasi Kelas')
                    ->icon('heroicon-m-book-open')
                    ->description('Detail lengkap kelas pelatihan')
                    ->collapsible()
                    ->schema([
                        // Header dengan Thumbnail
                        Grid::make(1)
                            ->schema([
                                ImageEntry::make('thumbnail_url')
                                    ->label('Thumbnail Kelas')
                                    ->columnSpanFull(),
                            ]),

                        // Informasi Dasar (3 kolom)
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('📌 Nama Kelas')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->columnSpanFull(),

                                TextEntry::make('course.title')
                                    ->label('📖 Kursus Utama')
                                    ->icon('heroicon-m-book-open')
                                    ->color('info'),

                                TextEntry::make('instructor.name')
                                    ->label('👨‍🏫 Instruktur')
                                    ->icon('heroicon-m-user-circle'),

                                TextEntry::make('category.name')
                                    ->label('🏷️ Kategori')
                                    ->icon('heroicon-m-tag')
                                    ->badge()
                                    ->color('primary'),
                            ]),

                        // Jadwal dan Lokasi (2 kolom)
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_date')
                                    ->label('📅 Tanggal Mulai')
                                    ->icon('heroicon-m-calendar')
                                    ->dateTime('d M Y H:i'),

                                TextEntry::make('end_date')
                                    ->label('🏁 Tanggal Berakhir')
                                    ->icon('heroicon-m-calendar')
                                    ->dateTime('d M Y H:i'),

                                TextEntry::make('meeting_link')
                                    ->label('🔗 Link Pertemuan')
                                    ->icon('heroicon-m-link')
                                    ->columnSpanFull()
                                    ->url(fn($state) => $state)
                                    ->openUrlInNewTab(),
                            ]),

                        // Kapasitas dan Peserta (2 kolom)
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('capacity_info')
                                    ->label('👥 Kapasitas Peserta')
                                    ->state(function ($record) {
                                        return "{$record->current_enrolled} / {$record->max_capacity} peserta";
                                    })
                                    ->icon('heroicon-m-users')
                                    ->color('success'),

                                TextEntry::make('is_active')
                                    ->label('✅ Status Kelas')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn($state) => $state ? 'Aktif' : 'Tidak Aktif')
                                    ->icon('heroicon-m-check-circle'),
                            ]),

                        // Harga dan Level (2 kolom)
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('price')
                                    ->label('💰 Harga Kelas')
                                    ->icon('heroicon-m-currency-dollar')
                                    ->money('IDR')
                                    ->color('warning'),

                                TextEntry::make('level')
                                    ->label('📊 Level Kesulitan')
                                    ->icon('heroicon-m-star')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'beginner' => 'info',
                                        'intermediate' => 'warning',
                                        'advanced' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                            ]),

                        // Deskripsi (Full Width)
                        TextEntry::make('description')
                            ->label('📝 Deskripsi Kelas')
                            ->icon('heroicon-m-document-text')
                            ->columnSpanFull()
                            ->prose(),
                    ])
                    ->columnSpanFull(),

                // ===== SECTION 2: STATISTIK PROGRES MAHASISWA =====
                Section::make('📊 Statistik Progres Mahasiswa')
                    ->icon('heroicon-m-chart-bar')
                    ->description('Daftar mahasiswa beserta persentase penyelesaian materi')
                    ->collapsible(false)
                    ->columnSpanFull()
                    ->schema([
                        ViewEntry::make('enrollments')
                            ->label(false)
                            ->columnSpanFull()
                            ->view('filament.resources.class-results.student-progress-table'),
                    ]),
            ]);
    }
}
