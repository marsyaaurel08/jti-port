<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestPendingOrders extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Pesanan Menunggu Verifikasi';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->whereNotNull('payment_proof')
                    ->where('status', 'pending')
                    ->latest()
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                ImageColumn::make('payment_proof')
                    ->label('Bukti Pembayaran'),
                TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('Detail')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders-histories.view', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
