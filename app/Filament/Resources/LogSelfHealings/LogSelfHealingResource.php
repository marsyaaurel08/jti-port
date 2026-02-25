<?php

namespace App\Filament\Resources\LogSelfHealings;

use App\Filament\Resources\LogSelfHealings\Pages\CreateLogSelfHealing;
use App\Filament\Resources\LogSelfHealings\Pages\EditLogSelfHealing;
use App\Filament\Resources\LogSelfHealings\Pages\ListLogSelfHealings;
use App\Filament\Resources\LogSelfHealings\Pages\ViewLogSelfHealing;
use App\Filament\Resources\LogSelfHealings\Schemas\LogSelfHealingForm;
use App\Filament\Resources\LogSelfHealings\Schemas\LogSelfHealingInfolist;
use App\Filament\Resources\LogSelfHealings\Tables\LogSelfHealingsTable;
use App\Models\LogSelfHealing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogSelfHealingResource extends Resource
{
    protected static ?string $model = LogSelfHealing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LogSelfHealingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LogSelfHealingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogSelfHealingsTable::configure($table);
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
            'index' => ListLogSelfHealings::route('/'),
            'create' => CreateLogSelfHealing::route('/create'),
            'view' => ViewLogSelfHealing::route('/{record}'),
            'edit' => EditLogSelfHealing::route('/{record}/edit'),
        ];
    }
}
