<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Actions\Impersonate as ImpersonateAction;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->colors([
                        'danger' => 'super_admin',
                        'warning' => 'admin',
                        'success' => 'editor',
                        'gray' => 'user',
                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Role'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('impersonate')
                    ->label('Impersonate')
                    ->icon('heroicon-o-finger-print')
                    ->action(function ($record) {
                        $impersonate = ImpersonateAction::make()
                            ->redirectTo(route('filament.admin.pages.dashboard'));

                        return $impersonate->impersonate($record);
                    })
                    ->visible(fn ($record) => auth()->user()->canImpersonate() && $record->canBeImpersonated()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
