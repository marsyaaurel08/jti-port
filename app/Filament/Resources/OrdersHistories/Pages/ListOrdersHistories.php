<?php

namespace App\Filament\Resources\OrdersHistories\Pages;

use App\Filament\Resources\OrdersHistories\OrdersHistoryResource;
use App\Filament\Widgets\SelfHealingExplanation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrdersHistories extends ListRecords
{
    protected static string $resource = OrdersHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            SelfHealingExplanation::make(),
        ];
    }
}
