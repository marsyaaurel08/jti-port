<?php

namespace App\Filament\Resources\EnrollmentClasses;

use App\Filament\Resources\EnrollmentClasses\Pages\CreateEnrollmentClass;
use App\Filament\Resources\EnrollmentClasses\Pages\EditEnrollmentClass;
use App\Filament\Resources\EnrollmentClasses\Pages\ListEnrollmentClasses;
use App\Filament\Resources\EnrollmentClasses\Pages\ViewEnrollmentClass;
use App\Filament\Resources\EnrollmentClasses\Schemas\EnrollmentClassForm;
use App\Filament\Resources\EnrollmentClasses\Schemas\EnrollmentClassInfolist;
use App\Filament\Resources\EnrollmentClasses\Tables\EnrollmentClassesTable;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class EnrollmentClassResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationLabel = 'Pendaftaran Kelas';
    protected static ?string $pluralLabel = 'Pendaftaran Kelas';
    protected static ?string $modelLabel = 'Pendaftaran Kelas';

    protected static string|UnitEnum|null $navigationGroup = 'Kelas';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function form(Schema $schema): Schema
    {
        return EnrollmentClassForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EnrollmentClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnrollmentClassesTable::configure($table);
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
            'index' => ListEnrollmentClasses::route('/'),
            'create' => CreateEnrollmentClass::route('/create'),
            'view' => ViewEnrollmentClass::route('/{record}'),
            'edit' => EditEnrollmentClass::route('/{record}/edit'),
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
        return parent::getEloquentQuery()
            ->whereNotNull('class_id');
    }
}
