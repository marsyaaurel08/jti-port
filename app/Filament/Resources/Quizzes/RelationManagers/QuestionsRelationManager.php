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
                            'drop_down'       => 'Drop Down',
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
                            // TextInput::make('points')
                            //     ->numeric()
                            //     ->default(1)
                            //     ->required(),
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

            /*
            |--------------------------------------------------------------------------
            | DROP DOWN
            |--------------------------------------------------------------------------
            */

            Section::make('Drop Down Configuration')
                ->visible(fn($get) => $get('type') === 'drop_down')
                ->schema([

                    Textarea::make('question_config.template')
                        ->label('Template')
                        ->required()
                        ->rows(6)
                        ->helperText('Gunakan {blank1}, {blank2}, dst. untuk menandai bagian yang harus diisi')
                        ->columnSpanFull(),

                    Toggle::make('question_config.code_mode')
                        ->label('Code Mode')
                        ->default(true)
                        ->hidden(),

                    Repeater::make('question_config.blanks')
                        ->label('Blanks')
                        ->schema([
                            TextInput::make('id')
                                ->label('Blank ID')
                                ->required(),

                            Repeater::make('options')
                                ->label('Options')
                                ->schema([
                                    TextInput::make('id')
                                        ->label('Option ID')
                                        ->required(),

                                    TextInput::make('text')
                                        ->label('Option Text')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->minItems(2)
                                ->maxItems(4),
                        ])
                        ->columns(1)
                        ->minItems(1)
                        ->columnSpanFull(),

                    KeyValue::make('answer_key.correct')
                        ->keyLabel('Blank ID')
                        ->valueLabel('Correct Option ID')
                        ->required()
                        ->columnSpanFull(),
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

        // Handle drop_down answer key
        if (isset($data['type']) && $data['type'] === 'drop_down') {
            if (!isset($data['answer_key']['correct'])) {
                $data['answer_key']['correct'] = [];
            }
            // Ensure it's an array for KeyValue component
            if (!is_array($data['answer_key']['correct'])) {
                $data['answer_key']['correct'] = [];
            }

            // Auto-generate option IDs when loading data
            if (isset($data['question_config']['blanks'])) {
                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                foreach ($data['question_config']['blanks'] as &$blank) {
                    if (isset($blank['options'])) {
                        foreach ($blank['options'] as $index => &$option) {
                            $option['id'] = $letters[$index] ?? chr(65 + $index);
                        }
                    }
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate option IDs for drop_down type
        if (isset($data['type']) && $data['type'] === 'drop_down') {
            if (isset($data['question_config']['blanks'])) {
                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                foreach ($data['question_config']['blanks'] as &$blank) {
                    if (isset($blank['options'])) {
                        foreach ($blank['options'] as $index => &$option) {
                            $option['id'] = $letters[$index] ?? chr(65 + $index);
                        }
                    }
                }
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-generate option IDs for drop_down type on edit
        if (isset($data['type']) && $data['type'] === 'drop_down') {
            if (isset($data['question_config']['blanks'])) {
                $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                foreach ($data['question_config']['blanks'] as &$blank) {
                    if (isset($blank['options'])) {
                        foreach ($blank['options'] as $index => &$option) {
                            $option['id'] = $letters[$index] ?? chr(65 + $index);
                        }
                    }
                }
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
                        'drop_down'       => 'Drop Down',
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
                    DeleteBulkAction::make()
                        ->after(function () {
                            // Update the quiz's total_score_weight after bulk deletion
                            $quiz = $this->getOwnerRecord();
                            if ($quiz) {
                                $totalScoreWeight = $quiz->questions()->sum('score_weight');
                                $quiz->total_score_weight = $totalScoreWeight;
                                $quiz->saveQuietly();
                            }
                        }),
                ]),
            ]);
    }
}
