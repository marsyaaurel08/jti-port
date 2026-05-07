<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom 1: Menampilkan Key
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),

                // Kolom 2: Menampilkan Value
                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->limit(50) // Membatasi teks jika value terlalu panjang
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            // Kolom 3: Action untuk mengedit value
            ->actions([
                Action::make('edit_value')
                    ->label('Edit Value')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->form([
                        Select::make('payment_gateway')
                            ->label('Payment Gateway')
                            ->options([
                                'bni' => 'BNI',
                                'midtrans' => 'Midtrans',
                            ])
                            ->default(fn (Setting $record) => $record->value)
                            ->required(),
                    ])
                    ->action(function (Setting $record, array $data): void {
                        // Menyimpan perubahan ke database
                        $record->update([
                            'value' => $data['payment_gateway'],
                        ]);
                    }),
            ]);
    }
}
