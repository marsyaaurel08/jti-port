<?php

namespace App\Filament\Resources\EnrollmentClasses\Pages;

use App\Filament\Resources\EnrollmentClasses\EnrollmentClassResource;
use App\Http\Controllers\EnrollmentController;
use Filament\Resources\Pages\CreateRecord;

class CreateEnrollmentClass extends CreateRecord
{
    protected static string $resource = EnrollmentClassResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['enrolled_at'] = $data['enrolled_at'] ?? now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $enrollment = $this->record;

        // Ambil course_id dari relasi class_id
        $courseId = $enrollment->class?->course_id;

        if ($courseId) {
            EnrollmentController::createEnrollmentOrder($enrollment, $courseId);
        }
    }
}
