<?php

namespace App\Filament\Resources\OrdersHistories\Pages;

use App\Filament\Resources\OrdersHistories\OrdersHistoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrdersHistory extends ViewRecord
{
    protected static string $resource = OrdersHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
