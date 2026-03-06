<?php

namespace App\Filament\Resources\EnrollmentCourses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentCourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(3)
                    ->schema([
                        // Kolom Kiri & Tengah: Detail Utama
                        Section::make('Informasi Akademik')
                            ->description('Detail siswa dan kursus yang diikuti')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama Siswa')
                                            ->icon('heroicon-m-user-circle')
                                            ->weight('bold'),

                                        TextEntry::make('course.title')
                                            ->label('Kursus')
                                            ->icon('heroicon-m-book-open')
                                            ->color('primary'),

                                        TextEntry::make('order.invoice_number')
                                            ->label('Nomor Invoice')
                                            ->icon('heroicon-m-ticket')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('course.level') // Tambahan info kursus
                                            ->label('Tingkat Kesulitan')
                                            ->icon('heroicon-m-chart-bar')
                                            ->placeholder('N/A'),
                                    ]),
                            ])
                            ->columnSpan(2),

                        // Kolom Kanan: Status & Waktu
                        Section::make('Waktu Akses')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextEntry::make('enrolled_at')
                                    ->label('Terdaftar')
                                    ->icon('heroicon-m-calendar-days')
                                    ->dateTime('d M Y'),

                                TextEntry::make('expires_at')
                                    ->label('Berakhir')
                                    ->icon('heroicon-m-calendar-date-range')
                                    ->dateTime('d M Y')
                                    ->color('danger'),                            ])
                            ->columnSpan(1),

                        // Baris Baru: Progress
                        Section::make('Progress Belajar')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('completed_lesson_count')
                                            ->label('Materi Selesai')
                                            ->icon('heroicon-m-check-badge')
                                            ->suffix(' Materi'),

                                        TextEntry::make('lastAccessedLesson.title')
                                            ->label('Terakhir Diakses')
                                            ->icon('heroicon-m-play-pause')
                                            ->placeholder('Belum dimulai'),

                                        IconEntry::make('is_completed')
                                            ->label('Status Lulus')
                                            ->boolean(),

                                        TextEntry::make('completed_at')
                                            ->label('Tanggal Lulus')
                                            ->icon('heroicon-m-trophy')
                                            ->dateTime('d M Y')
                                            ->placeholder('-'),
                                    ]),
                            ])
                            ->columnSpan(3),

                        // Baris Baru: Sertifikat (Hanya muncul jika sudah lulus)
                        Section::make('Sertifikasi')
                            ->icon('heroicon-o-document-check')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('certificate_code')
                                            ->label('Kode Sertifikat')
                                            ->icon('heroicon-m-qr-code')
                                            ->copyable()
                                            ->fontFamily('mono'),

                                        TextEntry::make('certificate_url')
                                            ->label('Dokumen')
                                            ->icon('heroicon-m-arrow-down-tray')
                                            ->formatStateUsing(fn () => 'Unduh Sertifikat')
                                            ->url(fn ($state) => $state, true)
                                            ->color('primary'),
                                    ]),
                            ])
                            ->columnSpan(3)
                            ->visible(fn ($record) => $record->is_completed),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
