<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label('Log Name')
                    ->badge()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subject Type')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('User')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('subject_type')
                    ->options([
                        'App\\Models\\User' => 'User',
                        'App\\Models\\Post' => 'Post',
                    ]),
                SelectFilter::make('log_name'),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalContent(function (Activity $record): string {
                        return view('filament.resources.activities.view-activity', ['activity' => $record])->render();
                    })
                    ->modalHeading('Activity Details')
                    ->modalSubmitActionLabel('Close')
                    ->modalCloseButton(false)
                    ->form([]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
