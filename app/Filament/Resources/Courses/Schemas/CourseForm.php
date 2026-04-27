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
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Detail Kursus')
                        ->description('Informasi dasar tentang kursus')
                        ->schema([
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->label('Kategori')
                                ->required(),
                            TextInput::make('title')
                                ->label('Judul Kursus')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($set, ?string $state) {
                                    $set('slug', Str::slug($state));
                                }),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->helperText('Slug akan diisi otomatis dari judul. Anda dapat mengubahnya jika diperlukan untuk URL yang lebih pendek atau SEO.')
                                ->required(),
                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->columnSpanFull(),
                            TextInput::make('price')
                                ->label('Harga')
                                ->required()
                                ->numeric()
                                ->default(0.0)
                                ->minValue(0)
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
                            // TextInput::make('total_duration')
                            //     ->label('Total Durasi (menit)')
                            //     ->required()
                            //     ->numeric()
                            //     ->default(0),
                            Hidden::make('total_lessons')
                                ->default(0),
                            // TextInput::make('avg_rating')
                            //     ->label('Rating Rata-rata')
                            //     ->required()
                            //     ->numeric()
                            //     ->default(0.0),
                        ]),
                    Step::make('Materi')
                        ->description('Tambahkan bab dan pelajaran')
                        ->schema([
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
                                                ->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function ($set, ?string $state) {
                                                    $set('slug', Str::slug($state));
                                                }),
                                            Select::make('type')
                                                ->label('Tipe Pelajaran')
                                                ->options(['video' => 'Video', 'text' => 'Teks', 'quiz' => 'Kuis'])
                                                ->required()
                                                ->live(),
                                            TextInput::make('slug')
                                                ->label('Slug')
                                                ->helperText('Slug akan diisi otomatis dari judul. Anda dapat mengubahnya jika diperlukan untuk URL yang lebih pendek atau SEO.')
                                                ->required(),
                                            TextInput::make('content_url')
                                                ->label('Content URL')
                                                ->visible(fn($get) => $get('type') === 'video')
                                                ->url(),
                                            RichEditor::make('content_text')
                                                ->label('Content Text')
                                                ->visible(fn($get) => $get('type') === 'text' || $get('type') === 'video'),
                                            // Text Position (visible for video type)
                                            Select::make('text_position')
                                                ->label('Posisi Teks')
                                                ->options([
                                                    'none' => 'Tanpa Teks',
                                                    'before' => 'Teks Sebelum Video',
                                                    'after' => 'Teks Setelah Video',
                                                    'both' => 'Teks Sebelum dan Sesudah Video',
                                                ])
                                                ->default('none')
                                                ->live()
                                                ->visible(fn($get) => $get('type') === 'video'),
                                            // Text After Video
                                            RichEditor::make('text_after_video')
                                                ->label('Teks Setelah Video')
                                                ->visible(fn($get) => $get('type') === 'video' && in_array($get('text_position'), ['after', 'both'])),
                                            // TextInput::make('duration_seconds')
                                            //     ->label('Durasi (menit)')
                                            //     ->numeric()
                                            //     ->required(),
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
                                                        ->default(60)
                                                        
                                                        ->nullable(),
                                                ]),
                                        ])->columnSpanFull(),
                                ]), // closes chapters repeater schema and chapters repeater
                        ])
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                        <x-filament::button
                            type="submit"
                            size="sm"
                        >
                            Submit
                        </x-filament::button>
                    BLADE)))
                    ->columnSpanFull()
            ]);
    }
}
