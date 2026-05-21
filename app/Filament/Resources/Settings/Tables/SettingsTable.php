<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->label('Group')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('edit_value')
                    ->label('Edit Value')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->form(function (Setting $record) {
                        // 1. Kondisi jika tipe datanya adalah 'image'
                        if (str_contains($record->key, 'logo') || str_contains($record->key, 'icon') || str_contains($record->key, 'image')) {
                            return [
                                FileUpload::make('value')
                                    ->label('Upload Gambar')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings') // File akan disimpan di storage/app/public/settings
                                    ->default($record->value) // Menampilkan preview gambar yang sudah ada
                                    ->required(),
                            ];
                        }

                        // 2. Kondisi khusus untuk key 'payment_gateway'
                        if ($record->key === 'payment_gateway') {
                            return [
                                Select::make('value')
                                    ->label('Payment Gateway')
                                    ->options([
                                        'bni' => 'BNI',
                                        'midtrans' => 'Midtrans',
                                    ])
                                    ->default($record->value)
                                    ->required(),
                            ];
                        }

                        // 3. Kondisi default (jika bukan image dan bukan payment_gateway)
                        return [
                            TextInput::make('value')
                                ->label('Value')
                                ->default($record->value)
                                ->required(),
                        ];
                    })
                    ->action(function (Setting $record, array $data): void {
                        // Sistem secara otomatis mendapatkan path gambar dari FileUpload
                        // atau string dari TextInput/Select dan menyimpannya ke database
                        $record->update([
                            'value' => $data['value'],
                        ]);

                        Cache::forget("setting_{$record->key}");
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
