<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->getStateUsing(function ($record) {
                        if ($record->subject) {
                            return sprintf(
                                '%s #%s',
                                class_basename($record->subject_type),
                                $record->subject_id
                            );
                        }
                        return '-';
                    }),
                TextColumn::make('event')
                    ->searchable(),
                TextColumn::make('causer_type')
                    ->searchable(),
                TextColumn::make('causer.name')
                    ->label('Causer')
                    ->default('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
