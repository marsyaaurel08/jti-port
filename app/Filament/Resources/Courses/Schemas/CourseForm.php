<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->schema([
                        Tab::make('Detail Kursus')->schema([
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->label('Kategori')
                                ->required(),
                            TextInput::make('title')
                                ->label('Judul Kursus')
                                ->required(),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->columnSpanFull(),
                            TextInput::make('price')
                                ->label('Harga')
                                ->required()
                                ->numeric()
                                ->default(0.0)
                                ->prefix('$'),
                            Select::make('level')
                                ->label('Level')
                                ->options(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'])
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('thumbnail')
                                ->label('Thumbnail')
                                ->collection('thumbnail')
                                ->image()
                                ->imageEditor()
                                ->preserveFilenames()
                                ->responsiveImages()
                                ->visibility('public'),
                            Select::make('status')
                                ->label('Status')
                                ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
                                ->default('draft')
                                ->required(),
                            TextInput::make('total_duration')
                                ->label('Total Durasi (menit)')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('total_lessons')
                                ->label('Total Pelajaran')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('avg_rating')
                                ->label('Rating Rata-rata')
                                ->required()
                                ->numeric()
                                ->default(0.0),
                        ]),
                        Tab::make('Materi')->schema([
                            Repeater::make('chapters')
                                ->relationship('chapters')
                                ->label('Bab')
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Judul Bab')
                                        ->required(),
                                    Repeater::make('lessons')
                                        ->relationship('lessons')
                                        ->label('Pelajaran')
                                        ->schema([
                                            TextInput::make('title')
                                                ->label('Judul Pelajaran')
                                                ->required(),
                                            Select::make('type')
                                                ->label('Tipe Pelajaran')
                                                ->options(['video' => 'Video', 'text' => 'Text', 'quiz' => 'Quiz'])
                                                ->required()
                                                ->live(),
                                            TextInput::make('slug')
                                                ->label('Slug')
                                                ->required(),
                                            TextInput::make('content_url')
                                                ->label('Content URL')
                                                ->visible(fn($get) => $get('type') === 'video')
                                                ->url(),
                                            RichEditor::make('content_text')
                                                ->label('Content Text')
                                                ->visible(fn($get) => $get('type') === 'text'),
                                            TextInput::make('duration_seconds')
                                                ->label('Durasi (menit)')
                                                ->numeric()
                                                ->required(),
                                            Select::make('is_free_preview')
                                                ->label('Preview Gratis')
                                                ->options([0 => 'Tidak', 1 => 'Ya'])
                                                ->required(),
                                            // Quiz Configuration
                                            Section::make('Konfigurasi Quiz')
                                                ->relationship('quiz')
                                                ->visible(fn($get) => $get('type') === 'quiz')
                                                ->schema([
                                                    TextInput::make('pass_score')
                                                        ->label('Nilai Lulus Minimum')
                                                        ->numeric()
                                                        ->default(70)
                                                        ->minValue(0)
                                                        ->maxValue(100)
                                                        ->required(),
                                                    TextInput::make('time_limit_minutes')
                                                        ->label('Batas Waktu (menit)')
                                                        ->numeric()
                                                        ->nullable(),
                                                    // Questions Repeater - nested inside quiz section
                                                    Section::make('Pertanyaan Quiz')
                                                        ->schema([
                                                            Repeater::make('questions')
                                                                ->label('Pertanyaan')
                                                                ->relationship()
                                                                ->schema([
                                                            TextInput::make('question_text')
                                                                ->label('Teks Pertanyaan')
                                                                ->required()
                                                                ->columnSpanFull(),
                                                            Select::make('type')
                                                                ->label('Tipe Pertanyaan')
                                                                ->options([
                                                                    'single_choice' => 'Single Choice (Pilihan Ganda)',
                                                                    'multiple_choice' => 'Multiple Choice (Pilihan Ganda Kompleks)',
                                                                    'boolean' => 'Benar/Salah',
                                                                    'essay' => 'Essay',
                                                                    'matching' => 'Matching (Pasangkan)',
                                                                    'drag_drop_zones' => 'Drag & Drop Zones',
                                                                    'fill_in_blank' => 'Fill in the Blank'
                                                                ])
                                                                ->required()
                                                                ->live()
                                                                ->columnSpanFull(),
                                                            TextInput::make('score_weight')
                                                                ->label('Bobot Nilai')
                                                                ->numeric()
                                                                ->default(1)
                                                                ->minValue(1),
                                                            Textarea::make('explanation')
                                                                ->label('Penjelasan')
                                                                ->rows(2),
                                                            Textarea::make('hint')
                                                                ->label('Petunjuk')
                                                                ->rows(2),

                                                            // Single Choice Options
                                                            Section::make('Opsi Jawaban (Single Choice)')
                                                                ->visible(fn($get) => $get('type') === 'single_choice')
                                                                ->schema([
                                                                    Repeater::make('options')
                                                                        ->label('Opsi')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Opsi')
                                                                                ->required()
                                                                                ->hint('Gunakan A, B, C, D, E untuk setiap opsi'),
                                                                            TextInput::make('text')
                                                                                ->label('Teks Osi')
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(2)
                                                                        ->defaultItems(5)
                                                                        ->minItems(5)
                                                                        ->maxItems(5),
                                                                    Toggle::make('shuffle_options')
                                                                        ->label('Acak Opsi')
                                                                        ->default(true),
                                                                    // Answer Key for Single Choice
                                                                    Select::make('answer_correct')
                                                                        ->label('Jawaban Benar (Pilih ID Opsi)')
                                                                        ->options([
                                                                            'A' => 'A',
                                                                            'B' => 'B',
                                                                            'C' => 'C',
                                                                            'D' => 'D',
                                                                            'E' => 'E',
                                                                        ])
                                                                        ->required()
                                                                        ->hint('Pilih ID opsi yang merupakan jawaban benar'),
                                                                ]),
                                                            // Multiple Choice Options
                                                            Section::make('Opsi Jawaban (Multiple Choice)')
                                                                ->visible(fn($get) => $get('type') === 'multiple_choice')
                                                                ->schema([
                                                                    Repeater::make('options')
                                                                        ->label('Opsi')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Opsi (A, B, C, D, dll)')
                                                                                ->required(),
                                                                            TextInput::make('text')
                                                                                ->label('Teks Opsi')
                                                                                ->required(),
                                                                            TextInput::make('points')
                                                                                ->label('Poin')
                                                                                ->numeric()
                                                                                ->default(1)
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(3)
                                                                        ->minItems(2),
                                                                    TextInput::make('min_correct')
                                                                        ->label('Minimal Jawaban Benar')
                                                                        ->numeric()
                                                                        ->default(1),
                                                                    TextInput::make('max_correct')
                                                                        ->label('Maksimal Jawaban Benar')
                                                                        ->numeric(),
                                                                    Toggle::make('partial_scoring')
                                                                        ->label('Penilaian Parsial')
                                                                        ->default(true),
                                                                    Toggle::make('shuffle_options')
                                                                        ->label('Acak Opsi')
                                                                        ->default(true),
                                                                    // Answer Key for Multiple Choice
                                                                    KeyValue::make('answer_correct_multiple')
                                                                        ->label('Jawaban Benar (Pilih ID yang benar)')
                                                                        ->keyLabel('ID Opsi')
                                                                        ->valueLabel('Pilih')
                                                                        ->required(),
                                                                ]),
                                                            // Boolean Answer
                                                            Section::make('Jawaban Benar/Salah')
                                                                ->visible(fn($get) => $get('type') === 'boolean')
                                                                ->schema([
                                                                    Select::make('answer_correct_boolean')
                                                                        ->label('Jawaban Benar')
                                                                        ->options([true => 'Benar', false => 'Salah'])
                                                                        ->required(),
                                                                ]),
                                                            // Essay Configuration
                                                            Section::make('Konfigurasi Essay')
                                                                ->visible(fn($get) => $get('type') === 'essay')
                                                                ->schema([
                                                                    TextInput::make('max_words')
                                                                        ->label('Maksimal Kata')
                                                                        ->numeric(),
                                                                    TextInput::make('min_words')
                                                                        ->label('Minimal Kata')
                                                                        ->numeric(),
                                                                    KeyValue::make('grading_criteria')
                                                                        ->label('Kriteria Penilaian')
                                                                        ->keyLabel('No')
                                                                        ->valueLabel('Kriteria'),
                                                                ]),
                                                            // Matching Configuration
                                                            Section::make('Konfigurasi Matching')
                                                                ->visible(fn($get) => $get('type') === 'matching')
                                                                ->schema([
                                                                    Repeater::make('matching_items')
                                                                        ->label('Item untuk Dipasangkan')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Item')
                                                                                ->required(),
                                                                            TextInput::make('text')
                                                                                ->label('Teks Item')
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(2)
                                                                        ->minItems(2),
                                                                    Repeater::make('matching_matches')
                                                                        ->label('Pasangan Jawaban')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Pasangan (A, B, C, dll)')
                                                                                ->required(),
                                                                            TextInput::make('text')
                                                                                ->label('Teks Pasangan')
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(2)
                                                                        ->minItems(2),
                                                                    Toggle::make('shuffle_items')
                                                                        ->label('Acak Item')
                                                                        ->default(true),
                                                                    Toggle::make('shuffle_matches')
                                                                        ->label('Acak Pasangan')
                                                                        ->default(true),
                                                                    // Answer Key for Matching
                                                                    KeyValue::make('answer_correct_matches')
                                                                        ->label('Jawaban Benar (ID Item -> ID Pasangan)')
                                                                        ->keyLabel('ID Item')
                                                                        ->valueLabel('ID Pasangan')
                                                                        ->required(),
                                                                ]),
                                                            // Drag Drop Sorting Configuration
                                                            Section::make('Konfigurasi Drag & Drop Sorting')
                                                                ->visible(fn($get) => $get('type') === 'drag_drop_sorting')
                                                                ->schema([
                                                                    Repeater::make('sorting_items')
                                                                        ->label('Item untuk Diurutkan')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Item')
                                                                                ->required(),
                                                                            TextInput::make('text')
                                                                                ->label('Teks Item')
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(2)
                                                                        ->minItems(2),
                                                                    Toggle::make('shuffle_items')
                                                                        ->label('Acak Item')
                                                                        ->default(true),
                                                                    Toggle::make('allow_partial_credit')
                                                                        ->label('Izinkan Nilai Parsial')
                                                                        ->default(true),
                                                                    // Answer Key for Sorting (correct order)
                                                                    TextInput::make('answer_correct_order')
                                                                        ->label('Urutan Benar (ID dipisahkan koma)')
                                                                        ->placeholder('step1,step2,step3,step4')
                                                                        ->required(),
                                                                ]),
                                                            // Drag Drop Zones Configuration
                                                            Section::make('Konfigurasi Drag & Drop Zones')
                                                                ->visible(fn($get) => $get('type') === 'drag_drop_zones')
                                                                ->schema([
                                                                    Repeater::make('zones')
                                                                        ->label('Zona/Area')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Zona')
                                                                                ->required(),
                                                                            TextInput::make('label')
                                                                                ->label('Label Zona')
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(2)
                                                                        ->minItems(1),
                                                                    Repeater::make('zone_items')
                                                                        ->label('Item untuk Ditarik')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Item')
                                                                                ->required(),
                                                                            TextInput::make('text')
                                                                                ->label('Teks Item')
                                                                                ->required(),
                                                                        ])
                                                                        ->columns(2)
                                                                        ->minItems(1),
                                                                    Toggle::make('shuffle_items')
                                                                        ->label('Acak Item')
                                                                        ->default(true),
                                                                    // Answer Key for Zones
                                                                    KeyValue::make('answer_correct_placements')
                                                                        ->label('Penempatan Benar (ID Item -> ID Zona)')
                                                                        ->keyLabel('ID Item')
                                                                        ->valueLabel('ID Zona')
                                                                        ->required(),
                                                                ]),
                                                            // Fill in Blank Configuration
                                                            Section::make('Konfigurasi Fill in the Blank')
                                                                ->visible(fn($get) => $get('type') === 'fill_in_blank')
                                                                ->schema([
                                                                    Textarea::make('template')
                                                                        ->label('Template Pertanyaan (gunakan {id} untuk blank)')
                                                                        ->placeholder('Contoh: The process of water changing from liquid to gas is called {evaporation}.')
                                                                        ->rows(2)
                                                                        ->required(),
                                                                    Repeater::make('blanks')
                                                                        ->label('Blank Definition')
                                                                        ->schema([
                                                                            TextInput::make('id')
                                                                                ->label('ID Blank')
                                                                                ->required(),
                                                                            Toggle::make('case_sensitive')
                                                                                ->label('Case Sensitive')
                                                                                ->default(false),
                                                                        ])
                                                                        ->columns(2),
                                                                    // Answer Key for Fill in Blank
                                                                    KeyValue::make('answer_correct_fill')
                                                                        ->label('Jawaban Benar (ID Blank -> Jawaban, gunakan | untuk multiple jawaban)')
                                                                        ->keyLabel('ID Blank')
                                                                        ->valueLabel('Jawaban')
                                                                        ->required(),
                                                                ]),
                                                            ])
                                                            ->columns(2)
                                                            ->columnSpanFull()
                                                            ->collapsible()
                                                            ->collapsed(),
                                                        ]),
                                                ]),
                                        ])->columnSpanFull(),
                                ]), // closes chapters repeater schema and chapters repeater
                        ]), // closes second Tab's schema and second Tab
                    ])->columnSpanFull() // applies to Tabs
            ]);
    }
}
