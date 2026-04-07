<?php

namespace App\Filament\Resources\Quizzes\RelationManagers;

use Filament\Forms\Components\{
    Select,
    Textarea,
    TextInput,
    Toggle,
    Repeater,
    CheckboxList,
    KeyValue
};
use Filament\Schemas\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\{
    CreateAction,
    EditAction,
    DeleteAction,
    BulkActionGroup,
    DeleteBulkAction
};

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Pertanyaan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | QUESTION
            |--------------------------------------------------------------------------
            */

            Section::make('Question Details')
                // ->label('Detail Pertanyaan')
                ->schema([

                    Textarea::make('question_text')
                        ->label('Teks Pertanyaan')
                        ->required()
                        ->columnSpanFull(),

                    Select::make('type')
                        ->options([
                            'single_choice'   => 'Single Choice',
                            'multiple_choice' => 'Multiple Choice',
                            'boolean'         => 'Boolean',
                            'matching'        => 'Matching',
                            'drag_drop_sorting' => 'Drag & Drop Sorting',
                        ])
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('score_weight')
                        ->label('Bobot Skor')
                        ->numeric()
                        ->default(1)
                        ->minValue(1),

                    Textarea::make('explanation')
                        ->label('Penjelasan Jawaban')
                        ->rows(2),
                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | SINGLE CHOICE
            |--------------------------------------------------------------------------
            */

            Section::make('Single Choice Configuration')
                ->visible(fn($get) => $get('type') === 'single_choice')
                ->schema([

                    Repeater::make('question_config.options')
                        ->default([
                            ['id' => 'A'],
                            ['id' => 'B'],
                            ['id' => 'C'],
                            ['id' => 'D'],
                            ['id' => 'E'],
                        ])
                        ->minItems(4)
                        ->schema([
                            TextInput::make('id')->disabled()->dehydrated(true),
                            TextInput::make('text')->required(),
                        ])
                        ->columns(2),

                    Toggle::make('question_config.shuffle_options')
                        ->default(true),

                    Select::make('answer_key.correct')
                        ->options([
                            'A' => 'A',
                            'B' => 'B',
                            'C' => 'C',
                            'D' => 'D',
                            'E' => 'E',
                        ])
                        ->required(),
                ]),

            /*
            |--------------------------------------------------------------------------
            | MULTIPLE CHOICE
            |--------------------------------------------------------------------------
            */

            Section::make('Multiple Choice Configuration')
                ->visible(fn($get) => $get('type') === 'multiple_choice')
                ->schema([

                    Repeater::make('question_config.options')
                        ->default([
                            ['id' => 'A'],
                            ['id' => 'B'],
                            ['id' => 'C'],
                            ['id' => 'D'],
                            ['id' => 'E'],
                        ])
                        ->minItems(4)
                        ->schema([
                            TextInput::make('id')->disabled()->dehydrated(true),
                            TextInput::make('text')->required(),
                            TextInput::make('points')
                                ->numeric()
                                ->default(1)
                                ->required(),
                        ])
                        ->columns(3),

                    // TextInput::make('question_config.min_correct')
                    //     ->numeric()
                    //     ->default(1),

                    // TextInput::make('question_config.max_correct')
                    //     ->numeric(),

                    // Toggle::make('question_config.partial_scoring')
                    //     ->default(true),

                    Toggle::make('question_config.shuffle_options')
                        ->default(true),

                    Select::make('answer_key.correct')
                        ->multiple()
                        ->live()
                        ->options(function (callable $get) {
                            $options = $get('question_config.options') ?? [];
                            $result = [];
                            foreach ($options as $option) {
                                if (isset($option['id'])) {
                                    $result[$option['id']] = $option['id'];
                                }
                            }
                            return $result;
                        })
                        ->required()
                        ->label('Jawaban Benar (Pilih ID yang benar)'),

                    // Select::make('answer_key.scoring')
                    //     ->options([
                    //         'partial' => 'Partial',
                    //         'full'    => 'Full Only',
                    //     ])
                    //     ->default('partial')
                    //     ->required(),
                ]),

            /*
            |--------------------------------------------------------------------------
            | BOOLEAN
            |--------------------------------------------------------------------------
            */

            Section::make('Boolean Configuration')
                ->visible(fn($get) => $get('type') === 'boolean')
                ->schema([

                    Select::make('answer_key.correct')
                        ->options([
                            true  => 'True',
                            false => 'False',
                        ])
                        ->required(),
                ]),


            /*
            |--------------------------------------------------------------------------
            | MATCHING
            |--------------------------------------------------------------------------
            */

            Section::make('Matching Configuration')
                ->visible(fn($get) => $get('type') === 'matching')
                ->schema([

                    Repeater::make('question_config.items')
                        ->schema([
                            TextInput::make('id')->required(),
                            TextInput::make('text')->required(),
                        ])
                        ->columns(2)
                        ->minItems(2),

                    Repeater::make('question_config.matches')
                        ->schema([
                            TextInput::make('id')->required(),
                            TextInput::make('text')->required(),
                        ])
                        ->columns(2)
                        ->minItems(2),

                    Toggle::make('question_config.shuffle_items')
                        ->default(true),

                    Toggle::make('question_config.shuffle_matches')
                        ->default(true),

                    KeyValue::make('answer_key.correct_matches')
                        ->keyLabel('Item ID')
                        ->valueLabel('Match ID')
                        ->required(),
                ]),

            /*
            |--------------------------------------------------------------------------
            | DRAG DROP SORTING
            |--------------------------------------------------------------------------
            */

            Section::make('Drag & Drop Sorting Configuration')
                ->visible(fn($get) => $get('type') === 'drag_drop_sorting')
                ->schema([

                    Repeater::make('question_config.items')
                        ->schema([
                            TextInput::make('id')->required(),
                            TextInput::make('text')->required(),
                        ])
                        ->columns(2)
                        ->minItems(2),

                    Toggle::make('question_config.shuffle_items')
                        ->default(true),

                    Toggle::make('question_config.allow_partial_credit')
                        ->default(true),

                    Select::make('answer_key.correct_order')
                        ->multiple()
                        ->options(function (callable $get) {
                            $items = $get('question_config.items') ?? [];
                            $result = [];
                            foreach ($items as $item) {
                                if (isset($item['id'])) {
                                    $result[$item['id']] = $item['id'];
                                }
                            }
                            return $result;
                        })
                        ->required()
                        ->label('Correct Order')
                        ->helperText('Urutkan sesuai jawaban benar'),
                ]),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Handle multiple choice answer key
        if (isset($data['type']) && $data['type'] === 'multiple_choice') {
            if (!isset($data['answer_key']['correct'])) {
                $data['answer_key']['correct'] = [];
            }
            // Ensure it's an array
            if (!is_array($data['answer_key']['correct'])) {
                $data['answer_key']['correct'] = [];
            }
        }
        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                TextColumn::make('question_text')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('type')
                    ->badge(),

                TextColumn::make('score_weight')
                    ->label('Points'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'single_choice'   => 'Single Choice',
                        'multiple_choice' => 'Multiple Choice',
                        'boolean'         => 'Boolean',
                        'essay'           => 'Essay',
                        'matching'        => 'Matching',
                        'fill_in_blank'   => 'Fill in Blank',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
