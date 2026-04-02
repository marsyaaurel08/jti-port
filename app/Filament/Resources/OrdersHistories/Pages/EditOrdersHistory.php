<?php

namespace App\Filament\Resources\OrdersHistories\Pages;

use App\Filament\Resources\OrdersHistories\OrdersHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOrdersHistory extends EditRecord
{
    protected static string $resource = OrdersHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
