<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Post Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        RichEditor::make('content')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'heading',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'undo',
                                'redo',
                            ]),

                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->columnSpan(2),

                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured-image')
                            ->label('Featured Image')
                            ->collection('featured-image')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120)
                            ->rules(['image', 'mimes:jpeg,png,webp']),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Gallery Images')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxFiles(10)
                            ->maxSize(5120),

                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('Attachments')
                            ->collection('attachments')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'application/zip'])
                            ->maxSize(10240),
                    ])
                    ->columnSpan(1),
            ]);
    }
}
