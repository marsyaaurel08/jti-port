<?php

namespace App\Filament\Resources\LogSelfHealings\Pages;

use App\Filament\Resources\LogSelfHealings\LogSelfHealingResource;
use App\Filament\Resources\SelfHealings\SelfHealingResource;
use App\Filament\Widgets\SelfHealingExplanation;
use Filament\Resources\Pages\ListRecords;

class ListLogSelfHealings extends ListRecords
{
    protected static string $resource = LogSelfHealingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    public function getBreadcrumbs(): array
    {
        return [
            SelfHealingResource::getUrl() => 'Self Healing',
            LogSelfHealingResource::getUrl('index') => 'Log Self Healing',
            '#' => 'Daftar',
        ];
    }
}
