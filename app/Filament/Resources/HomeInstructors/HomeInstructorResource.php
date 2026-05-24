<?php

namespace App\Filament\Resources\HomeInstructors;

use App\Filament\Resources\HomeInstructors\Pages\ListHomeInstructors;
use App\Filament\Resources\HomeInstructors\Schemas\HomeInstructorForm;
use App\Filament\Resources\HomeInstructors\Tables\HomeInstructorsTable;
use App\Models\HomeInstructor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomeInstructorResource extends Resource
{
    protected static ?string $model = HomeInstructor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Instruktur Beranda';
    protected static bool $isGloballySearchable = false;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return HomeInstructorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeInstructorsTable::configure($table);
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
            'index' => ListHomeInstructors::route('/'),
            // 'create' => CreateHomeInstructor::route('/create'),
            // 'edit' => EditHomeInstructor::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
