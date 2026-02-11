<?php

namespace App\Filament\Resources\EnrollmentClasses\Pages;

use App\Filament\Resources\EnrollmentClasses\EnrollmentClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnrollmentClasses extends ListRecords
{
    protected static string $resource = EnrollmentClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
