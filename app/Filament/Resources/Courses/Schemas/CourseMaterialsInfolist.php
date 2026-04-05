<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Filament\Resources\Quizzes\QuizResource;
use App\Models\Quiz;
use Filament\Actions\Action;
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
                Section::make('📚 Detail Materi')
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
                                        // TextEntry::make('total_duration')
                                        //     ->label('Total Durasi')
                                        //     ->formatStateUsing(fn($record): string => floor($record->lessons->sum('duration_seconds') / 60) . ' Menit')
                                        //     ->badge()
                                        //     ->color('gray'),
                                    ]),
                                RepeatableEntry::make('lessons')
                                    ->label('Materi')
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
                                                // TextEntry::make('duration_seconds')
                                                //     ->label('Durasi')
                                                //     ->formatStateUsing(fn(int $state): string => floor($state / 60) . 'm ' . ($state % 60) . 's')
                                                //     ->badge()
                                                //     ->color('gray'),
                                                TextEntry::make('is_free_preview')
                                                    ->label('Preview Gratis')
                                                    ->badge()
                                                    ->color(fn(bool $state): string => $state ? 'success' : 'gray')
                                                    ->formatStateUsing(fn(bool $state): string => $state ? 'Ya' : 'Tidak'),
                                                Action::make('manage_quiz')
                                                    ->label('Manage Quiz')
                                                    ->icon('heroicon-o-academic-cap')
                                                    ->color('warning')
                                                    ->visible(fn($record): bool => $record->type === 'quiz')
                                                    ->url(function ($record) {
                                                        // Get or create quiz for this lesson
                                                        $quiz = $record->quiz ?? Quiz::create([
                                                            'lesson_id' => $record->id,
                                                            'pass_score' => 70,
                                                            'time_limit_minutes' => null,
                                                        ]);

                                                        return QuizResource::getUrl('edit', ['record' => $quiz->id]);
                                                    }),
                                            ])
                                            ->extraAttributes(['class' => 'bg-gray-50 rounded-lg p-3']),
                                    ]),
                            ])
                            ->extraAttributes(['class' => 'border rounded-lg p-4 mb-4']),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
