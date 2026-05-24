<?php

namespace App\Filament\Resources\SelfHealings;

use App\Filament\Resources\LogSelfHealings\LogSelfHealingResource;
use App\Filament\Resources\SelfHealings\Pages\CreateSelfHealing;
use App\Filament\Resources\SelfHealings\Pages\EditSelfHealing;
use App\Filament\Resources\SelfHealings\Pages\ListSelfHealings;
use App\Filament\Resources\SelfHealings\Schemas\SelfHealingForm;
use App\Filament\Resources\SelfHealings\Tables\SelfHealingsTable;
use App\Models\Order;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SelfHealingResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $pluralLabel = 'Self-Healing';
    protected static string|UnitEnum|null $navigationGroup = 'Transactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'Self Healing';

    public static function form(Schema $schema): Schema
    {
        return SelfHealingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SelfHealingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationItems(): array
    {
        $listActiveRoutes = [
            'filament.admin.resources.self-healings.*',
            'filament.admin.resources.log-self-healings.*',
        ];
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->icon(static::$navigationIcon)
                ->url(static::getUrl())
                ->group(static::$navigationGroup)
                ->isActiveWhen(fn(): bool => request()->routeIs($listActiveRoutes)),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSelfHealings::route('/'),
            // 'create' => CreateSelfHealing::route('/create'),
            // 'edit' => EditSelfHealing::route('/{record}/edit'),
        ];
    }
}
