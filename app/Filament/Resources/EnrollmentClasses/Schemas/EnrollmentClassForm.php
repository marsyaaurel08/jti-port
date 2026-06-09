<?php

namespace App\Filament\Resources\EnrollmentClasses\Schemas;

use Filament\Forms\Components\{DateTimePicker, Repeater, Select, TextInput};
use Filament\Schemas\Components\{Section, View};
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

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
                            ->placeholder('Cari nama kelas...')
                            ->searchable()
                            ->preload()
                            ->required('Kelas harus dipilih')
                            ->validationMessages([
                                'required' => 'Kelas tidak boleh kosong, silakan pilih kelas terlebih dahulu.',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (! $state) {
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

                        View::make('filament.components.quota-full-warning')
                            ->hidden(fn(Get $get) => empty($get('class_id')) || (int) $get('available_slots') > 0),

                        Repeater::make('students')
                            ->label('Daftar Siswa')
                            ->hidden(fn(Get $get) => empty($get('class_id')) || (int) $get('available_slots') <= 0)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship(
                                        name: 'user',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->withoutRole(['super_admin', 'pengajar'])
                                    )
                                    ->label('Nama Siswa')
                                    ->placeholder('Cari nama siswa...')
                                    ->searchable()
                                    ->preload()
                                    ->required('Siswa harus dipilih')
                                    ->validationMessages([
                                        'required' => 'Nama siswa tidak boleh kosong.',
                                    ])
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
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
