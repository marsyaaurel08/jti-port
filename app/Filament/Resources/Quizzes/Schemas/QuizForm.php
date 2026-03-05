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
                ComponentsSection::make('Quiz Settings')
                    ->description('Configure the quiz passing score and time limit')
                    ->schema([
                        TextInput::make('pass_score')
                            ->label('Passing Score (%)')
                            ->helperText('Minimum score required to pass the quiz')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(70)
                            ->suffix('%'),

                        TextInput::make('time_limit_minutes')
                            ->label('Time Limit (Minutes)')
                            ->helperText('Leave empty for unlimited time')
                            ->numeric()
                            ->minValue(1)
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
