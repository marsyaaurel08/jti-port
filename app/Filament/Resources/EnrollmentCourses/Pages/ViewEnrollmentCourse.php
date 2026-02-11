<?php

namespace App\Filament\Resources\EnrollmentCourses\Pages;

use App\Filament\Resources\EnrollmentCourses\EnrollmentCourseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEnrollmentCourse extends ViewRecord
{
    protected static string $resource = EnrollmentCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
