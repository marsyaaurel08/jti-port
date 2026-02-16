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

                                // KOLOM KIRI: Repeater Siswa
                                Repeater::make('students')
                                    ->label('Pilih Siswa')
                                    ->minItems(1)
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship(
                                                'user',
                                                'name',
                                                fn($query) => $query->whereHas('roles', fn($q) => $q->whereNotIn('name', ['admin', 'super_admin', 'pengajar']))
                                            )
                                            ->label('Nama Siswa')
                                            ->searchable()
                                            ->required()
                                            ->distinct(),
                                    ])
                                    ->addActionLabel('Tambah Siswa'),

                                // KOLOM KANAN: Pilih Kursus
                                Select::make('course_id')
                                    ->relationship('course', 'title', fn($query) => $query->where('status', 'published'))
                                    ->label('Pilih Kursus (untuk semua siswa)')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
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
