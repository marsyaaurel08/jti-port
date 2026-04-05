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
                            // TextInput::make('total_duration')
                            //     ->label('Total Durasi (menit)')
                            //     ->required()
                            //     ->numeric()
                            //     ->default(0),
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
                                                        ->nullable(),
                                                ]),
                                        ])->columnSpanFull(),
                                ]), // closes chapters repeater schema and chapters repeater
                        ]), // closes second Tab's schema and second Tab
                    ])->columnSpanFull() // applies to Tabs
            ]);
    }
}
