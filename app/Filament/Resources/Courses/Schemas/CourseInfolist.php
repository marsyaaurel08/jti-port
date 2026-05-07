<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Kursus')
                    ->icon('heroicon-m-book-open')
                    ->description('Detail lengkap course')
                    ->collapsible()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                ImageEntry::make('thumbnail_url')
                                    ->label('Thumbnail')
                                    ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Judul Kursus')
                                    // ->size('text-lg')
                                    ->weight('font-bold')
                                    ->icon('heroicon-o-book-open')
                                    ->columnSpanFull(),
                                TextEntry::make('category.name')
                                    ->label('Kategori')
                                    ->icon('heroicon-o-tag'),
                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->icon('heroicon-o-document-text')
                                    ->columnSpanFull(),
                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->money('IDR'),
                                TextEntry::make('level')
                                    ->label('Level')
                                    ->icon('heroicon-o-academic-cap')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'beginner' => 'success',
                                        'intermediate' => 'warning',
                                        'advanced' => 'danger',
                                    }),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->icon(fn(string $state): string => match ($state) {
                                        'draft' => 'heroicon-o-eye-slash',
                                        'published' => 'heroicon-o-eye',
                                        'archived' => 'heroicon-o-archive-box',
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'published' => 'success',
                                        'archived' => 'warning',
                                    }),
                                TextEntry::make('total_lessons')
                                    ->label('Total Pelajaran')
                                    ->icon('heroicon-o-document-text'),
                                TextEntry::make('avg_rating')
                                    ->label('Rating Rata-rata')
                                    ->formatStateUsing(fn(float $state): string => number_format($state, 1) . ' / 5.0')
                                    ->icon('heroicon-o-star'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
