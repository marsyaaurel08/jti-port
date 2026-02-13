<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

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
                            TextInput::make('thumbnail_url')
                                ->label('Thumbnail URL')
                                ->url(),
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
                                        ->label('Pelajaran')
                                        ->schema([
                                            TextInput::make('title')
                                                ->label('Judul Pelajaran')
                                                ->required(),
                                            Select::make('type')
                                                ->label('Tipe Pelajaran')
                                                ->options(['video' => 'Video', 'article' => 'Article', 'quiz' => 'Quiz'])
                                                ->required(),
                                            TextInput::make('slug')
                                                ->label('Slug')
                                                ->required(),
                                            TextInput::make('content_url')
                                                ->label('Content URL')
                                                ->url(),
                                            Textarea::make('content_text')
                                                ->label('Content Text'),
                                            TextInput::make('duration_seconds')
                                                ->label('Durasi (menit)')
                                                ->numeric()
                                                ->required(),
                                            Select::make('is_free_preview')
                                                ->label('Preview Gratis')
                                                ->options([0 => 'Tidak', 1 => 'Ya'])
                                                ->required(),
                                            TextInput::make('sort_order')
                                                ->label('Urutan')
                                                ->numeric()
                                                ->required(),
                                        ])->columnSpanFull(),
                                ]), // closes chapters repeater schema and chapters repeater
                        ]), // closes second Tab's schema and second Tab
                    ])->columnSpanFull() // applies to Tabs
            ]);
    }
}
