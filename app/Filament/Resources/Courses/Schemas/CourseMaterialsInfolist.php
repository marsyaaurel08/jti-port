<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseMaterialsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('📚 Course Materials')
                    ->icon('heroicon-m-academic-cap')
                    ->description('Semua bab dan materi dalam kursus ini')
                    ->schema([
                        RepeatableEntry::make('chapters')
                            ->label('Chapters')
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('title')
                                            ->label('Judul Bab')
                                            ->weight('bold')
                                            ->size('text-base')
                                            ->columnSpanFull(),
                                        TextEntry::make('lessons_count')
                                            ->label('Total Materi')
                                            ->formatStateUsing(fn($record): string => $record->lessons->count() . ' Materi')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('total_duration')
                                            ->label('Total Durasi')
                                            ->formatStateUsing(fn($record): string => floor($record->lessons->sum('duration_seconds') / 60) . ' Menit')
                                            ->badge()
                                            ->color('gray'),
                                    ]),
                                RepeatableEntry::make('lessons')
                                    ->label('Lessons')
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->label('Tipe')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'video' => 'info',
                                                        'text' => 'success',
                                                        'quiz' => 'warning',
                                                    })
                                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                                                TextEntry::make('title')
                                                    ->label('Judul Materi')
                                                    ->weight('medium')
                                                    ->size('text-sm')
                                                    ->columnSpanFull(),
                                                TextEntry::make('content_url')
                                                    ->label('URL Konten')
                                                    ->visible(fn($record): bool => $record->type === 'video')
                                                    ->formatStateUsing(fn($state): string => $state ? str($state)->limit(80) : '-')
                                                    ->url(fn($state): string => $state)
                                                    ->openUrlInNewTab()
                                                    ->copyable(),
                                                TextEntry::make('content_text')
                                                    ->label('Isi Konten')
                                                    ->visible(fn($record): bool => $record->type === 'text')
                                                    ->markdown()
                                                    ->columnSpanFull(),
                                                TextEntry::make('duration_seconds')
                                                    ->label('Durasi')
                                                    ->formatStateUsing(fn(int $state): string => floor($state / 60) . 'm ' . ($state % 60) . 's')
                                                    ->badge()
                                                    ->color('gray'),
                                                TextEntry::make('is_free_preview')
                                                    ->label('Preview Gratis')
                                                    ->badge()
                                                    ->color(fn(bool $state): string => $state ? 'success' : 'gray')
                                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Ya' : 'Tidak'),
                                            ])
                                            ->columnSpanFull()
                                            ->extraAttributes(['class' => 'bg-gray-50 rounded-lg p-3']),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'border rounded-lg p-4 mb-4']),
                    ]),
            ]);
    }
}
