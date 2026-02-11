<?php

namespace App\Filament\Resources\EnrollmentCourses\Pages;

use App\Filament\Resources\EnrollmentCourses\EnrollmentCourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnrollmentCourses extends ListRecords
{
    protected static string $resource = EnrollmentCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
