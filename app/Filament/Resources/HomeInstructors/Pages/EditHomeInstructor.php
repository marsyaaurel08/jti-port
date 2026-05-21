<?php

namespace App\Filament\Resources\HomeInstructors\Pages;

use App\Filament\Resources\HomeInstructors\HomeInstructorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeInstructor extends EditRecord
{
    protected static string $resource = HomeInstructorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
