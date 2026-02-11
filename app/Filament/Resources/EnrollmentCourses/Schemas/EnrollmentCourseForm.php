<?php

namespace App\Filament\Resources\EnrollmentCourses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentCourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pendaftaran Kursus Mandiri (Admin)')
                    ->description('Mendaftarkan siswa langsung ke katalog kursus.')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Nama Siswa')
                            ->searchable()
                            ->preload()
                            ->required(), // Wajib diisi

                        Select::make('course_id')
                            ->relationship('course', 'title', fn($query) => $query->where('status', 'published'))
                            ->label('Pilih Kursus')
                            ->searchable()
                            ->preload()
                            ->required(), // Wajib diisi (bukan opsional)

                        DateTimePicker::make('enrolled_at')
                            ->label('Tanggal Mulai Akses')
                            ->default(now())
                            ->required(),

                        DateTimePicker::make('expires_at')
                            ->label('Batas Akhir Akses')
                            ->helperText('Isi jika kursus ini memiliki masa aktif terbatas.'),

                        // Inisialisasi progress ke 0
                        Hidden::make('completed_lesson_count')->default(0),
                        Hidden::make('is_completed')->default(false),
                    ])->columnSpanFull()
            ]);
    }
}
