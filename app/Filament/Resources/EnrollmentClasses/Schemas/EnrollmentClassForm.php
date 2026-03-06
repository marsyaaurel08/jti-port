<?php

namespace App\Filament\Resources\EnrollmentClasses\Schemas;

use App\Models\CourseClass;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        Select::make('class_id')
                            ->relationship('class', 'title', fn($query) => $query->where('is_active', true))
                            ->label('Pilih Kelas')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) {
                                    $set('available_slots', 0);
                                    return;
                                }

                                $class = \App\Models\CourseClass::find($state);
                                if ($class) {
                                    // Mengambil fungsi available_slots() dari model
                                    $set('available_slots', $class->available_slots());
                                }
                            }),

                        TextInput::make('available_slots')
                            ->label('Slot Tersedia')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix('Sisa:')
                            ->suffix(' Kursi'),

                        Repeater::make('students')
                            ->label('Daftar Siswa')
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('Nama Siswa')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                DateTimePicker::make('expires_at')
                                    ->label('Masa Berlaku Akses')
                                    ->helperText('Kosongkan jika Lifetime.'),
                            ])
                            ->addActionLabel('Tambah Siswa')
                            // LOGIKA PEMBATASAN TOMBOL TAMBAH
                            ->maxItems(function (Get $get) {
                                $slots = (int) $get('available_slots');
                                return $slots > 0 ? $slots : 0;
                            })
                            
                            ->minItems(1)
                            ->collapsed()
                            ->cloneable()
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
