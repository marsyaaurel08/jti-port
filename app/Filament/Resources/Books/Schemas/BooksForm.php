<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BooksForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Book Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(9999),
                        Textarea::make('summary')
                            ->nullable(),
                    ]),
            ]);
    }
}
