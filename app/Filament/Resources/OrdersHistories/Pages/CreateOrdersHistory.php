<?php

namespace App\Filament\Resources\OrdersHistories\Pages;

use App\Filament\Resources\OrdersHistories\OrdersHistoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrdersHistory extends CreateRecord
{
    protected static string $resource = OrdersHistoryResource::class;
}
