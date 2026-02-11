<?php

namespace App\Filament\Resources\EnrollmentClasses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pendaftaran Kelas (Admin)')
                    ->description('Pendaftaran manual oleh admin untuk siswa ke kelas spesifik.')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Nama Siswa')
                            ->searchable()
                            ->preload()
                            ->required(), // Wajib diisi

                        Select::make('class_id')
                            ->relationship('class', 'title', fn($query) => $query->where('is_active', true))
                            ->label('Pilih Kelas')
                            ->searchable()
                            ->preload()
                            ->required(), // Wajib diisi sesuai permintaan Anda

                        DateTimePicker::make('enrolled_at')
                            ->label('Tanggal Terdaftar')
                            ->default(now())
                            ->required(),

                        DateTimePicker::make('expires_at')
                            ->label('Masa Berlaku Akses')
                            ->helperText('Kosongkan jika akses selamanya (Lifetime).'), // Penjelasan fungsi expires_at

                        // Hidden fields untuk memenuhi syarat progress 0
                        Hidden::make('completed_lesson_count')->default(0),
                        Hidden::make('is_completed')->default(false),
                    ])->columnSpanFull(),
            ]);
    }
}
