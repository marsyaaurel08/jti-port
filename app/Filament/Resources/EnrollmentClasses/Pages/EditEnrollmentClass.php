<?php

namespace App\Filament\Resources\EnrollmentClasses\Pages;

use App\Filament\Resources\EnrollmentClasses\EnrollmentClassResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEnrollmentClass extends EditRecord
{
    protected static string $resource = EnrollmentClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
