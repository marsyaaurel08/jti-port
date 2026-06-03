<?php

namespace App\Filament\Resources\Classes\Schemas;

use App\Models\CourseClass;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ClassesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        // Kolom Kiri & Tengah: Detail Utama (Span 2)
                        Section::make('Informasi Utama')
                            ->schema([
                                TextInput::make('title')
                                    ->required('Nama kelas harus diisi')
                                    ->validationMessages([
                                        'required' => 'Nama kelas tidak boleh kosong.',
                                    ])
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn(string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),

                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required('Slug harus diisi')
                                    ->validationMessages([
                                        'required' => 'Slug tidak boleh kosong.',
                                        'unique' => 'Slug ini sudah digunakan oleh kelas lain.',
                                    ])
                                    ->unique(CourseClass::class, 'slug', ignoreRecord: true),

                                RichEditor::make('description')
                                    ->required('Deskripsi kelas harus diisi')
                                    ->validationMessages([
                                        'required' => 'Deskripsi kelas tidak boleh kosong.',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        // Kolom Kanan: Media & Status (Span 1)
                        Section::make('Media & Status')
                            ->schema([
                                FileUpload::make('thumbnail_url')
                                    ->label('Thumbnail Kelas')
                                    ->image()
                                    ->disk('public')
                                    ->directory('course-classes'),

                                Toggle::make('is_active')
                                    ->label('Aktifkan Kelas')
                                    ->default(true)
                                    ->helperText('Hanya kelas aktif yang muncul di katalog.'),
                            ])
                            ->columnSpan(1),

                        // Section Bawah: Relasi & Logistik
                        Section::make('Detail Kursus & Pengaturan')
                            ->schema([
                                Select::make('course_id')
                                    ->live()
                                    ->relationship('course', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required('Kursus harus dipilih')
                                    ->validationMessages([
                                        'required' => 'Kursus tidak boleh kosong, pilih kursus terlebih dahulu.',
                                    ])
                                    ->afterStateUpdated(fn($state, $set) => $set('category', \App\Models\Course::find($state)?->category?->name)),

                                Select::make('instructor_id')
                                    ->relationship('instructor', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required('Instruktur harus dipilih')
                                    ->validationMessages([
                                        'required' => 'Instruktur tidak boleh kosong, pilih instruktur terlebih dahulu.',
                                    ]),

                                TextInput::make('category')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                        if ($record && $record->course) {
                                            $component->state($record->course->category?->name);
                                        }
                                    }),

                                Select::make('level')
                                    ->options([
                                        'beginner' => 'Beginner',
                                        'intermediate' => 'Intermediate',
                                        'advanced' => 'Advanced',
                                    ])
                                    ->required('Level kelas harus dipilih')
                                    ->validationMessages([
                                        'required' => 'Level kelas tidak boleh kosong, pilih level terlebih dahulu.',
                                    ]),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        // Section Samping: Harga & Kapasitas
                        Section::make('Biaya & Kapasitas')
                            ->schema([
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->required('Harga kelas harus diisi')
                                    ->validationMessages([
                                        'required' => 'Harga kelas tidak boleh kosong.',
                                        'numeric' => 'Harga harus berupa angka.',
                                    ]),

                                TextInput::make('max_capacity')
                                    ->numeric()
                                    ->label('Kapasitas Maksimal')
                                    ->required('Kapasitas maksimal harus diisi')
                                    ->validationMessages([
                                        'required' => 'Kapasitas maksimal tidak boleh kosong.',
                                        'numeric' => 'Kapasitas harus berupa angka.',
                                    ]),

                                TextInput::make('current_enrolled')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(), // Biasanya diupdate lewat sistem pendaftaran
                            ])
                            ->columnSpan(1),

                        // Section Full: Waktu & Link
                        Section::make('Jadwal & Link Pertemuan')
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->required('Tanggal mulai kelas harus diisi')
                                    ->validationMessages([
                                        'required' => 'Tanggal mulai kelas tidak boleh kosong.',
                                    ]),

                                DateTimePicker::make('end_date')
                                    ->required('Tanggal akhir kelas harus diisi')
                                    ->validationMessages([
                                        'required' => 'Tanggal akhir kelas tidak boleh kosong.',
                                        'after' => 'Tanggal akhir harus lebih akhir dari tanggal mulai.',
                                    ])
                                    ->after('start_date'),

                                TextInput::make('meeting_link')
                                    ->url()
                                    ->placeholder('https://zoom.us/j/...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
