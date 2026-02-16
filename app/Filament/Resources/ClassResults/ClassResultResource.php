<?php

namespace App\Filament\Resources\ClassResults;

use App\Filament\Resources\ClassResults\Pages\CreateClassResult;
use App\Filament\Resources\ClassResults\Pages\EditClassResult;
use App\Filament\Resources\ClassResults\Pages\ListClassResults;
use App\Filament\Resources\ClassResults\Pages\ViewClassResult;
use App\Filament\Resources\ClassResults\Schemas\ClassResultForm;
use App\Filament\Resources\ClassResults\Schemas\ClassResultInfolist;
use App\Filament\Resources\ClassResults\Tables\ClassResultsTable;
use App\Models\ClassResult;
use App\Models\CourseClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClassResultResource extends Resource
{
    protected static ?string $model = CourseClass::class;
    protected static ?string $navigationLabel = 'Class Proggress';
    protected static ?string $pluralLabel = 'Class Proggress';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    public static function form(Schema $schema): Schema
    {
        return ClassResultForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClassResultInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassResultsTable::configure($table);
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
            'index' => ListClassResults::route('/'),
            // 'create' => CreateClassResult::route('/create'),
            'view' => ViewClassResult::route('/{record}'),
            // 'edit' => EditClassResult::route('/{record}/edit'),
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
