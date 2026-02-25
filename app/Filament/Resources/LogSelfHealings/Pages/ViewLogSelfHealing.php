<?php

namespace App\Filament\Resources\LogSelfHealings\Pages;

use App\Filament\Resources\LogSelfHealings\LogSelfHealingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLogSelfHealing extends ViewRecord
{
    protected static string $resource = LogSelfHealingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
