<?php

namespace App\Filament\Resources\OrdersHistories\Pages;

use App\Filament\Resources\OrdersHistories\OrdersHistoryResource;
use App\Filament\Widgets\SelfHealingExplanation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrdersHistories extends ListRecords
{
    protected static string $resource = OrdersHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-list-bullet')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->orderByRaw('payment_proof IS NOT NULL DESC')
                ),
            'pending' => Tab::make('Menunggu Pembayaran')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->where('status', 'pending')
                        ->orderByRaw('payment_proof IS NOT NULL DESC, created_at DESC')
                ),
            'paid' => Tab::make('Berhasil Dibayar')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid')),
            'cancelled' => Tab::make('Dibatalkan')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SelfHealingExplanation::make(),
        ];
    }
}
