<?php

namespace App\Filament\Resources\Quizzes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizzesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson.chapter.course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('lesson.chapter.title')
                    ->label('Chapter')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('lesson.title')
                    ->label('Lesson')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('questions_count')
                    ->label('Questions')
                    ->counts('questions')
                    ->badge()
                    ->color('info'),

                TextColumn::make('pass_score')
                    ->label('Pass Score')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => $state >= 70 ? 'success' : 'warning')
                    ->sortable(),

                TextColumn::make('time_limit_minutes')
                    ->label('Time Limit')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' min' : 'Unlimited')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
