<?php

namespace App\Filament\Resources\OrdersHistories;

use App\Filament\Resources\OrdersHistories\Pages\CreateOrdersHistory;
use App\Filament\Resources\OrdersHistories\Pages\EditOrdersHistory;
use App\Filament\Resources\OrdersHistories\Pages\ListOrdersHistories;
use App\Filament\Resources\OrdersHistories\Pages\ViewOrdersHistory;
use App\Filament\Resources\OrdersHistories\Schemas\OrdersHistoryForm;
use App\Filament\Resources\OrdersHistories\Schemas\OrdersHistoryInfolist;
use App\Filament\Resources\OrdersHistories\Tables\OrdersHistoriesTable;
use App\Models\Order;
use App\Models\OrdersHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrdersHistoryResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|UnitEnum|null $navigationGroup = 'Transactions';

    protected static ?string $pluralLabel = 'Riwayat Transaksi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return OrdersHistoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrdersHistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersHistoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrdersHistories::route('/'),
            // 'create' => CreateOrdersHistory::route('/create'),
            'view' => ViewOrdersHistory::route('/{record}'),
            // 'edit' => EditOrdersHistory::route('/{record}/edit'),
        ];
    }
}
