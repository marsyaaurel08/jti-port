<?php

namespace App\Filament\Resources\ClassResults\Pages;

use App\Filament\Resources\ClassResults\ClassResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassResults extends ListRecords
{
    protected static string $resource = ClassResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
