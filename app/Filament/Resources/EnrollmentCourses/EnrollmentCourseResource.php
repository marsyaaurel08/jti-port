<?php

namespace App\Filament\Resources\EnrollmentCourses;

use App\Filament\Resources\EnrollmentCourses\Pages\CreateEnrollmentCourse;
use App\Filament\Resources\EnrollmentCourses\Pages\EditEnrollmentCourse;
use App\Filament\Resources\EnrollmentCourses\Pages\ListEnrollmentCourses;
use App\Filament\Resources\EnrollmentCourses\Pages\ViewEnrollmentCourse;
use App\Filament\Resources\EnrollmentCourses\Schemas\EnrollmentCourseForm;
use App\Filament\Resources\EnrollmentCourses\Schemas\EnrollmentCourseInfolist;
use App\Filament\Resources\EnrollmentCourses\Tables\EnrollmentCoursesTable;
use App\Models\Enrollment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class EnrollmentCourseResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationLabel = 'Course Enrollments';
    protected static ?string $pluralLabel = 'Course Enrollments';
    protected static ?string $modelLabel = 'Course Enrollments';
    protected static string|UnitEnum|null $navigationGroup = 'Courses';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function form(Schema $schema): Schema
    {
        return EnrollmentCourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EnrollmentCourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnrollmentCoursesTable::configure($table);
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
            'index' => ListEnrollmentCourses::route('/'),
            'create' => CreateEnrollmentCourse::route('/create'),
            'view' => ViewEnrollmentCourse::route('/{record}'),
            'edit' => EditEnrollmentCourse::route('/{record}/edit'),
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
            ->whereNull('class_id');
    }
}
