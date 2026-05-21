<?php

namespace App\Filament\Resources\HomeInstructors\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class HomeInstructorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan foto instruktur dalam bentuk lingkaran
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')), // Opsional jika ingin ada gambar default

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'), // Ditebalkan agar nama lebih menonjol

                TextColumn::make('role')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),

                // Bio biasanya panjang, kita sembunyikan secara default (bisa dimunculkan via tombol toggle columns)
                TextColumn::make('bio')
                    ->label('Bio')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('linkedin')
                    ->label('LinkedIn URL')
                    ->searchable()
                    ->copyable() // Memudahkan admin untuk copy link URL
                    ->copyMessage('LinkedIn URL copied')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->sortable(),

                // Menggunakan ToggleColumn agar admin bisa langsung mengubah status dari tabel
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc') // Mengurutkan berdasarkan sort_order secara otomatis
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                // Menggunakan Action biasa seperti idemu
                Action::make('edit_modal')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    // 1. Isi form dengan data record yang sedang diklik
                    ->fillForm(fn($record): array => $record->toArray())
                    // 2. Buat Form-nya
                    ->form([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('role')->required()->maxLength(255),
                        FileUpload::make('image')->image()->directory('instructors')->required(),
                        Textarea::make('bio')->maxLength(65535)->columnSpanFull(),
                        TextInput::make('linkedin')->url()->maxLength(255),
                        TextInput::make('sort_order')->numeric()->default(0),
                        Toggle::make('is_active')->label('Active Status'),
                    ])
                    // 3. Simpan perubahannya
                    ->action(function ($record, array $data): void {
                        $record->update($data);
                    }),
                DeleteAction::make(), // Ditambahkan agar bisa hapus per baris
                RestoreAction::make(), // Ditambahkan agar bisa restore per baris dari tong sampah
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
