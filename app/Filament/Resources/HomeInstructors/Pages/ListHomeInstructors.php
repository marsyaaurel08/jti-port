<?php

namespace App\Filament\Resources\HomeInstructors\Pages;

use App\Filament\Resources\HomeInstructors\HomeInstructorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeInstructors extends ListRecords
{
    protected static string $resource = HomeInstructorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
