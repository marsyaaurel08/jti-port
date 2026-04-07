<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Pengaturan Kuis')

                    ->description('Konfigurasi nilai kelulusan dan batas waktu kuis')
                    ->schema([
                        TextInput::make('pass_score')
                            ->label('Nilai Kelulusan (%)')
                            ->helperText('Nilai minimum yang diperlukan untuk lulus kuis')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(70)
                            ->suffix('%'),

                        TextInput::make('time_limit_minutes')
                            ->label('Batas Waktu (Menit)')
                            ->helperText('Kosongkan untuk waktu tak terbatas')
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
