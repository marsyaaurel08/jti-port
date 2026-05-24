<?php

namespace App\Filament\Resources\SelfHealings\Pages;

use App\Filament\Resources\SelfHealings\SelfHealingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSelfHealing extends EditRecord
{
    protected static string $resource = SelfHealingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
