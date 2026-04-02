<?php

namespace App\Filament\Resources\EnrollmentCourses\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentCourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pendaftaran Kursus Mandiri (Admin)')
                    ->description('Mendaftarkan 1 atau lebih siswa ke katalog kursus yang sama.')
                    ->schema([
                        // Kita bungkus dalam Grid dengan 2 kolom
                        Grid::make(2)
                            ->schema([
                                // KOLOM KIRI: Pilih Kursus
                                Select::make('course_id')
                                    ->relationship('course', 'title', fn($query) => $query->where('status', 'published'))
                                    ->label('Pilih Kursus')
                                    ->placeholder('Cari judul kursus...')
                                    ->helperText('Hanya kursus berstatus "Published" yang muncul.')
                                    ->prefixIcon('heroicon-m-book-open') // Ikon buku di dalam field
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),

                                // KOLOM KANAN: Repeater Siswa
                                Repeater::make('students')
                                    ->label('Daftar Siswa')
                                    ->minItems(1)
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship(
                                                'user',
                                                'name',
                                                fn($query) => $query->where('role', 'user')
                                            )
                                            ->label('Nama Siswa')
                                            ->placeholder('Pilih siswa...')
                                            ->prefixIcon('heroicon-m-user') // Ikon user di dalam field
                                            ->searchable()
                                            ->required()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(), // Mencegah duplikasi visual
                                    ])
                                    ->itemLabel(fn(array $state): ?string => $state['user_id'] ?? 'Siswa Baru') // Label dinamis tiap baris
                                    ->addActionLabel('Tambah Siswa Lagi')
                                    ->collapsible() // Bisa di-collapse jika daftar sudah panjang
                                    ->columnSpan(2),
                            ]),

                        // Hidden fields tetap di luar grid agar tidak merusak layout
                        Hidden::make('enrolled_at')->default(now()),
                        Hidden::make('expires_at')->default(null),
                        Hidden::make('completed_lesson_count')->default(0),
                        Hidden::make('is_completed')->default(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
