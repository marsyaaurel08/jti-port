<?php
namespace App\Filament\Resources\EnrollmentClasses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentClassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)
                    ->schema([

                        // KIRI: Detail Siswa & Status (8 Kolom)
                        Section::make('Informasi Pendaftaran')
                            ->description('Detail siswa dan status akses kelas.')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama Siswa')
                                            ->icon('heroicon-m-user')
                                            ->weight('bold'),

                                        TextEntry::make('user.email')
                                            ->label('Email')
                                            ->icon('heroicon-m-envelope')
                                            ->color('gray'),

                                        TextEntry::make('class.title')
                                            ->label('Kelas yang Diikuti')
                                            ->icon('heroicon-m-presentation-chart-bar')
                                            ->color('primary')
                                            ->weight('bold'),

                                        TextEntry::make('class.instructor.name')
                                            ->label('Instruktur Pengajar')
                                            ->icon('heroicon-m-academic-cap')
                                            ->placeholder('Belum ditentukan'),
                                    ]),
                            ])
                            ->columnSpan(8),

                        // KANAN: Masa Berlaku (4 Kolom)
                        Section::make('Masa Berlaku')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextEntry::make('enrolled_at')
                                    ->label('Mulai Terdaftar')
                                    ->icon('heroicon-m-calendar-days')
                                    ->dateTime('d M Y H:i'),

                                TextEntry::make('expires_at')
                                    ->label('Akses Berakhir')
                                    ->icon('heroicon-m-calendar-date-range')
                                    ->dateTime('d M Y H:i')
                                    ->color(fn ($state) => $state && $state < now() ? 'danger' : 'info')
                                    ->placeholder('Akses Selamanya (Lifetime)'),
                            ])
                            ->columnSpan(4),

                        // BAWAH: Detail Jadwal Kelas (Full Width)
                        Section::make('Detail Jadwal & Lokasi Kelas')
                            ->description('Informasi jadwal pelaksanaan kelas dari sistem.')
                            ->icon('heroicon-o-video-camera')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('class.start_date')
                                            ->label('Kelas Dimulai')
                                            ->icon('heroicon-m-play-circle')
                                            ->dateTime('d M Y'),

                                        TextEntry::make('class.end_date')
                                            ->label('Kelas Berakhir')
                                            ->icon('heroicon-m-stop-circle')
                                            ->dateTime('d M Y'),

                                        TextEntry::make('class.meeting_link')
                                            ->label('Link Pertemuan (Zoom/Meet)')
                                            ->icon('heroicon-m-link')
                                            ->url(fn ($state) => $state, true)
                                            ->color('primary')
                                            ->placeholder('Link belum tersedia')
                                            ->weight('bold'),
                                    ]),
                            ])
                            ->columnSpan(12),

                        // STATUS KELULUSAN (Jika Ada)
                        Section::make('Status Progres')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        IconEntry::make('is_completed')
                                            ->label('Status Lulus')
                                            ->boolean(),

                                        TextEntry::make('completed_at')
                                            ->label('Waktu Lulus')
                                            ->dateTime('d M Y H:i')
                                            ->placeholder('Belum Lulus'),

                                        TextEntry::make('certificate_code')
                                            ->label('Kode Sertifikat')
                                            ->copyable()
                                            ->fontFamily('mono')
                                            ->placeholder('-'),

                                        TextEntry::make('order.invoice_number')
                                            ->label('Nomor Invoice')
                                            ->badge()
                                            ->color('success'),
                                    ]),
                            ])
                            ->columnSpan(12),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
