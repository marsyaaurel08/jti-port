<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Kursus')
                    ->icon('heroicon-m-book-open')
                    ->description('Detail lengkap kursus')
                    ->collapsible()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('thumbnail')
                                    ->label('Thumbnail')
                                    ->collection('thumbnail')
                                    
                                    ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Judul Kursus')
                                    ->size('text-lg')
                                    ->weight('font-bold')
                                    ->columnSpanFull(),
                                TextEntry::make('slug')
                                    ->label('Slug'),
                                TextEntry::make('category.name')
                                    ->label('Kategori'),
                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->markdown()
                                    ->columnSpanFull(),
                                TextEntry::make('price')
                                    ->label('Harga')
                                    ->money(),
                                TextEntry::make('level')
                                    ->label('Level')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'beginner' => 'success',
                                        'intermediate' => 'warning',
                                        'advanced' => 'danger',
                                    }),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'published' => 'success',
                                        'archived' => 'warning',
                                    }),
                                TextEntry::make('total_duration')
                                    ->label('Total Durasi')
                                    ->formatStateUsing(fn(int $state): string => $state . ' menit'),
                                TextEntry::make('total_lessons')
                                    ->label('Total Pelajaran'),
                                TextEntry::make('avg_rating')
                                    ->label('Rating Rata-rata')
                                    ->formatStateUsing(fn(float $state): string => number_format($state, 1) . ' / 5.0'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
