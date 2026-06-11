<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Schemas\SettingForm;
use App\Filament\Resources\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Pengaturan Situs';
    protected static ?string $pluralLabel = 'Pengaturan Situs';
    protected static ?string $modelLabel = 'Pengaturan Situs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $recordTitleAttribute = 'Setting';

    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }
    public static function getNavigationItems(): array
    {
        $listActiveRoutes = [
            'filament.admin.resources.home-instructors.*',
            'filament.admin.resources.log-home-instructors.*',
        ];
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->icon(static::$navigationIcon)
                ->url(static::getUrl())
                ->group(static::$navigationGroup)
                ->isActiveWhen(fn(): bool => request()->routeIs($listActiveRoutes)),
        ];
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
            'index' => ListSettings::route('/'),
            // 'create' => CreateSetting::route('/create'),
            // 'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
