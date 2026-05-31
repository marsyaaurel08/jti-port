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
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ClassResultResource extends Resource
{
    protected static ?string $model = ClassResult::class;

    protected static ?string $navigationLabel = 'Progres Kelas';

    protected static ?string $pluralLabel = 'Progres Kelas';

    protected static ?string $modelLabel = 'Progres Kelas';

    protected static string|UnitEnum|null $navigationGroup = 'Kelas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check()) {
            if (str_contains(auth()->user()->email ?? '', '@admin')) {
                return $query;
            }

            return $query->where('instructor_id', auth()->id());
        }

        return $query;
    }
}
