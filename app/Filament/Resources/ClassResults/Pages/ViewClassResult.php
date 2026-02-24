<?php

namespace App\Filament\Resources\ClassResults\Pages;

use App\Filament\Resources\ClassResults\ClassResultResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClassResult extends ViewRecord
{
    protected static string $resource = ClassResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
