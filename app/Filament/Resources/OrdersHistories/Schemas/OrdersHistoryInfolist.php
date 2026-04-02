<?php

namespace App\Filament\Resources\OrdersHistories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;

class OrdersHistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: HEADER UTAMA
                Section::make('Informasi Pesanan')
                    ->description('Detail status dan identifikasi transaksi pelanggan.')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Blok Kiri: Data Identitas (Column Span 2)
                                Group::make([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('invoice_number')
                                                ->label('ID Transaksi')
                                                ->weight(FontWeight::Bold)
                                                ->color('primary')
                                                ->icon('heroicon-m-hashtag')
                                                ->copyable(),

                                            TextEntry::make('status')
                                                ->badge()
                                                ->icon(fn(string $state): string => match ($state) {
                                                    'success' => 'heroicon-m-check-circle',
                                                    'pending' => 'heroicon-m-clock',
                                                    'failed' => 'heroicon-m-x-circle',
                                                    default => 'heroicon-m-minus-circle',
                                                })
                                                ->color(fn(string $state): string => match ($state) {
                                                    'success' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('user.name')
                                                ->label('Nama Pelanggan')
                                                ->icon('heroicon-m-user-circle')
                                                ->weight(FontWeight::Medium),

                                            TextEntry::make('payment_method')
                                                ->label('Metode Pembayaran')
                                                ->icon('heroicon-m-credit-card')
                                                ->formatStateUsing(fn($state) => strtoupper($state ?? 'N/A')),

                                            TextEntry::make('paid_at')
                                                ->label('Tanggal Pembayaran')
                                                ->icon('heroicon-m-calendar-days')
                                                ->dateTime('d M Y, H:i')
                                                ->placeholder('Menunggu Konfirmasi Pembayaran')
                                                ->color('gray'),
                                        ]),
                                ])->columnSpanFull(),
                            ]),
                    ]),

                // SECTION 2: DAFTAR ITEM (TABLE STYLE)
                Section::make('Rincian Pembelian')
                    ->icon('heroicon-o-list-bullet')
                    ->description('Materi atau kursus yang termasuk dalam invoice ini.')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->label(false)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('item_name')
                                            ->label('Produk')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->icon('heroicon-m-play-circle')
                                            ->url(fn($record) => $record->course_id ? "/admin/courses/{$record->course_id}" : null)
                                            ->suffix(fn($record) => $record->class ? " ({$record->class->name})" : ''),

                                        TextEntry::make('class.name')
                                            ->label('Tipe')
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('General Access'),

                                        TextEntry::make('price_at_purchase')
                                            ->label('Harga Satuan')
                                            ->money('IDR')
                                            ->weight(FontWeight::Medium),

                                        TextEntry::make('id')
                                            ->label('Akses')
                                            ->formatStateUsing(fn() => 'Buka Materi')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->color('success')
                                            ->url(fn($record) => $record->course_id ? "/admin/courses/{$record->course_id}" : null),
                                    ]),
                            ])
                    ]),

                // SECTION 3: RINGKASAN FINANSIAL
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('subtotal')
                                    ->label('Subtotal (Bruto)')
                                    ->money('IDR')
                                    ->color('gray'),

                                TextEntry::make('discount_amount')
                                    ->label('Potongan Diskon')
                                    ->money('IDR')
                                    ->color('danger')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('total_amount')
                                    ->label('Total Akhir')
                                    ->money('IDR')
                                    ->weight(FontWeight::ExtraBold)
                                    ->color('success')
                                    ->size(TextSize::Large), // Pastikan menggunakan TextEntrySize
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
