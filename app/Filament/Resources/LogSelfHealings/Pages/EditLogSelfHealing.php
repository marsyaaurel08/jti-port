<?php

namespace App\Filament\Resources\LogSelfHealings\Pages;

use App\Filament\Resources\LogSelfHealings\LogSelfHealingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLogSelfHealing extends EditRecord
{
    protected static string $resource = LogSelfHealingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
            // DeleteAction::make(),
        ];
    }
}
