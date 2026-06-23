<?php

namespace App\Filament\Resources\Promos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Promo')
                    ->searchable()
                    ->sortable()
                    ->copyable() // Admin bisa klik untuk salin kode kupon langsung
                    ->fontFamily('mono')
                    ->weight('bold'),

                TextColumn::make('name')
                    ->label('Nama Kampanye')
                    ->searchable()
                    ->description(fn($record) => $record->for_all_products
                        ? '✨ Berlaku Global (Semua Item)'
                        : '🔒 Terbatas pada Item Tertentu'),

                TextColumn::make('value')
                    ->label('Potongan')
                    ->badge()
                    ->color(fn($record) => $record->type === 'percentage' ? 'warning' : 'success')
                    ->formatStateUsing(function ($record, $state) {
                        return $record->type === 'fixed'
                            ? 'Rp ' . number_format($state, 0, ',', '.')
                            : number_format($state, 0) . '%';
                    })
                    ->sortable(),

                TextColumn::make('usage_limit')
                    ->label('Kuota Terpakai')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        // Logic tracking kuota bisa disematkan di sini
                        $used = 0;
                        $max = $record->max_usage ?? '∞';
                        return "{$used} / {$max}";
                    }),

                TextColumn::make('end_date')
                    ->label('Masa Berlaku')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->description(fn($record) => 'Mulai: ' . $record->start_date->format('d M Y')),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Kupon')
                    ->boolean()
                    ->trueLabel('Aktif Saja')
                    ->falseLabel('Non-Aktif Saja'),

                TernaryFilter::make('for_all_products')
                    ->label('Cakupan Batasan')
                    ->trueLabel('Global')
                    ->falseLabel('Spesifik Item'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
