<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\HomeInstructors\HomeInstructorResource;
use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
            Action::make('home_instructors')
                ->label('Instruktur Beranda')
                ->url(HomeInstructorResource::getUrl('index'))
                ->icon('heroicon-o-users'),
        ];
    }
}
