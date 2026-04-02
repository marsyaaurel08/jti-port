<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected static ?string $maxWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('materials')
                ->label('View Course Materials')
                ->icon('heroicon-m-academic-cap')
                ->url(fn(): string => route('filament.admin.resources.courses.materials', $this->record)),
            Action::make('progress')
                ->label('View Progress')
                ->icon('heroicon-m-chart-pie')
                ->color('info')
                ->url(fn(): string => route('filament.admin.resources.courses.progress', $this->record)),
            EditAction::make(),
        ];
    }
}
