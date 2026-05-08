<?php

namespace App\Filament\Resources\Quizzes\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                ->schema([

                    Textarea::make('question_text')
                        ->label('Teks Pertanyaan')
                        ->required()
                        ->columnSpanFull(),

                    Select::make('type')
                        ->label('Tipe Pertanyaan')
                        ->options([
                            'single_choice'    => 'Pilihan Ganda',
                            'multiple_choice'  => 'Pilihan Ganda Kompleks',
                            'boolean'          => 'Benar/Salah',
                            'matching'         => 'Drag & Drop Mencocokkan',
                            'drag_drop_sorting' => 'Drag & Drop Mengurutan',
                            'drop_down'        => 'Drop Down Melengkali Kode',
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
                        ->label('Penjelasan')
                        ->rows(2),
                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | SINGLE CHOICE
            |--------------------------------------------------------------------------
            */

            Section::make('Konfigurasi Pilihan Ganda')
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
                        ->addable(false)
                        ->deletable(false)
                        ->label('Pilihan Jawaban')
                        ->schema([
                            TextInput::make('id')->disabled()->dehydrated(true)->label('ID Pilihan'),
                            TextInput::make('text')->required()->label('Teks Pilihan'),
                        ])
                        ->columns(2),

                    // Virtual field — hanya untuk UI, dipetakan ke answer_key.correct di processAnswerKey()
                    Select::make('answer_key_correct_single')
                        ->label('Jawaban Benar')
                        ->options([
                            'A' => 'A',
                            'B' => 'B',
                            'C' => 'C',
                            'D' => 'D',
                            'E' => 'E',
                        ])
                        ->dehydrated(true)
                        ->required()
                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                            $answerKey = $get('answer_key') ?? [];
                            if (isset($answerKey['correct']) && ! is_array($answerKey['correct'])) {
                                $set('answer_key_correct_single', $answerKey['correct']);
                            }
                        }),
                ]),

            /*
            |--------------------------------------------------------------------------
            | MULTIPLE CHOICE
            |--------------------------------------------------------------------------
            */

            Section::make('Konfigurasi Pilihan Ganda Kompleks')
                ->visible(fn($get) => $get('type') === 'multiple_choice')
                ->schema([
                    Repeater::make('question_config.options')
                        ->label('Pilihan Jawaban')
                        ->default([
                            ['id' => 'A'],
                            ['id' => 'B'],
                            ['id' => 'C'],
                            ['id' => 'D'],
                            ['id' => 'E'],
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->schema([
                            TextInput::make('id')->disabled()->dehydrated(true)->label('ID Pilihan'),
                            TextInput::make('text')->required()->label('Teks Pilihan'),
                        ])
                        ->columns(2),

                    // Virtual field
                    Select::make('answer_key_correct_multiple')
                        ->multiple()
                        ->live()
                        ->options(function (callable $get) {
                            $options = $get('question_config.options') ?? [];
                            $result  = [];
                            foreach ($options as $option) {
                                if (isset($option['id'])) {
                                    $result[$option['id']] = $option['id'];
                                }
                            }
                            return $result;
                        })
                        ->dehydrated(true)
                        ->required()
                        ->label('Jawaban Benar (Pilih ID benar lebih dari satu)')
                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                            $answerKey = $get('answer_key') ?? [];
                            if (isset($answerKey['correct']) && is_array($answerKey['correct'])) {
                                $set('answer_key_correct_multiple', $answerKey['correct']);
                            }
                        }),
                ]),

            /*
            |--------------------------------------------------------------------------
            | BOOLEAN
            |--------------------------------------------------------------------------
            */

            Section::make('Konfigurasi Benar/Salah')
                ->visible(fn($get) => $get('type') === 'boolean')
                ->schema([
                    // Virtual field
                    Select::make('answer_key_correct_boolean')
                        ->label('Jawaban')
                        ->options([
                            '1' => 'Benar',
                            '0' => 'Salah',
                        ])
                        ->dehydrated(true)
                        ->required()
                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                            $answerKey = $get('answer_key') ?? [];
                            if (isset($answerKey['correct'])) {
                                $set('answer_key_correct_boolean', $answerKey['correct'] ? '1' : '0');
                            }
                        }),
                ]),

            /*
            |--------------------------------------------------------------------------
            | MATCHING
            |--------------------------------------------------------------------------
            */

            Section::make('Konfigurasi Mencocokkan')
                ->visible(fn($get) => $get('type') === 'matching')
                ->schema([
                    Repeater::make('question_config.items')
                        ->default([
                            ['id' => 'A'],
                            ['id' => 'B'],
                            ['id' => 'C'],
                            ['id' => 'D'],
                        ])
                        ->label('Items')
                        ->schema([
                            TextInput::make('id')->required()->label('ID Item'),
                            TextInput::make('text')->required()->label('Teks Item'),
                        ])
                        ->columns(2)
                        ->minItems(2),

                    Repeater::make('question_config.matches')
                        ->default([
                            ['id' => '1'],
                            ['id' => '2'],
                            ['id' => '3'],
                            ['id' => '4'],
                        ])
                        ->schema([
                            TextInput::make('id')->required()->label('ID Match'),
                            TextInput::make('text')->required()->label('Teks Match'),
                        ])
                        ->columns(2)
                        ->minItems(2),

                    KeyValue::make('answer_key.correct_matches')
                        ->keyLabel('ID Item (contoh: A)')
                        ->valueLabel('ID Match (contoh: 1)')
                        ->required(),
                ]),

            /*
            |--------------------------------------------------------------------------
            | DRAG DROP SORTING
            |--------------------------------------------------------------------------
            */

            Section::make('Konfigurasi Drag & Drop Sorting')
                ->visible(fn($get) => $get('type') === 'drag_drop_sorting')
                ->schema([

                    Repeater::make('question_config.items')
                        ->schema([
                            TextInput::make('id')->required()->label('ID Item'),
                            TextInput::make('text')->required()->label('Teks Item'),
                        ])
                        ->columns(2)
                        ->minItems(2),

                    Toggle::make('question_config.shuffle_items')
                        ->label('Acak Urutan Item')
                        ->default(true),

                    Toggle::make('question_config.allow_partial_credit')
                        ->default(true)
                        ->hidden(),

                    Select::make('answer_key.correct_order')
                        ->multiple()
                        ->options(function (callable $get) {
                            $items  = $get('question_config.items') ?? [];
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

            Section::make('Konfigurasi Drop Down Melengkapi Kode')
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
                                ->helperText('Untuk konfigurasi bagian yang kosong (contoh : blank1)')
                                ->required(),

                            Repeater::make('options')
                                ->label('Options')
                                ->default([
                                    ['id' => 'A'],
                                    ['id' => 'B'],
                                    ['id' => 'C'],
                                ])
                                ->schema([
                                    TextInput::make('id')
                                        ->label('Option ID')
                                        ->required(),
                                    TextInput::make('text')
                                        ->label('Pilihan Jawaban')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->minItems(2)
                                ->maxItems(4),
                        ])
                        ->columns(1)
                        ->minItems(1)
                        ->columnSpanFull(),

                    // Virtual field
                    KeyValue::make('answer_key_correct_dropdown')
                        ->keyLabel('ID Blank (contoh: blank1)')
                        ->valueLabel('ID Option (contoh: A)')
                        ->dehydrated(true)
                        ->required()
                        ->columnSpanFull()
                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                            $answerKey = $get('answer_key') ?? [];
                            if (isset($answerKey['correct']) && is_array($answerKey['correct'])) {
                                $set('answer_key_correct_dropdown', $answerKey['correct']);
                            }
                        }),
                ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Proses answer_key dari virtual fields
    |--------------------------------------------------------------------------
    */

    private function processAnswerKey(array $data): array
    {
        if (! isset($data['type'])) {
            return $data;
        }

        switch ($data['type']) {
            case 'single_choice':
                $data['answer_key']['correct'] = $data['answer_key_correct_single'] ?? null;
                break;

            case 'multiple_choice':
                $data['answer_key']['correct'] = array_values($data['answer_key_correct_multiple'] ?? []);
                break;

            case 'boolean':
                $data['answer_key']['correct'] = ($data['answer_key_correct_boolean'] ?? '0') === '1';
                break;

            case 'drop_down':
                $data['answer_key']['correct'] = $data['answer_key_correct_dropdown'] ?? [];
                break;
        }

        unset(
            $data['answer_key_correct_single'],
            $data['answer_key_correct_multiple'],
            $data['answer_key_correct_boolean'],
            $data['answer_key_correct_dropdown'],
        );

        // Auto-generate option IDs untuk drop_down
        if ($data['type'] === 'drop_down' && isset($data['question_config']['blanks'])) {
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            foreach ($data['question_config']['blanks'] as &$blank) {
                foreach ($blank['options'] ?? [] as $index => &$option) {
                    $option['id'] = $letters[$index] ?? chr(65 + $index);
                }
            }
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Saat form dibuka untuk edit
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Auto-generate option IDs saat load data drop_down
        if (isset($data['type']) && $data['type'] === 'drop_down') {
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            foreach ($data['question_config']['blanks'] ?? [] as &$blank) {
                foreach ($blank['options'] ?? [] as $index => &$option) {
                    $option['id'] = $letters[$index] ?? chr(65 + $index);
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
                        'single_choice'     => 'Single Choice',
                        'multiple_choice'   => 'Multiple Choice',
                        'boolean'           => 'Boolean',
                        'essay'             => 'Essay',
                        'matching'          => 'Matching',
                        'fill_in_blank'     => 'Fill in Blank',
                        'drop_down'         => 'Drop Down',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(fn(array $data): array => $this->processAnswerKey($data)),
            ])
            ->actions([
                EditAction::make()
                    ->mutateFormDataUsing(fn(array $data): array => $this->processAnswerKey($data)),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function () {
                            $quiz = $this->getOwnerRecord();
                            if ($quiz) {
                                $totalScoreWeight          = $quiz->questions()->sum('score_weight');
                                $quiz->total_score_weight  = $totalScoreWeight;
                                $quiz->saveQuietly();
                            }
                        }),
                ]),
            ]);
    }
}
