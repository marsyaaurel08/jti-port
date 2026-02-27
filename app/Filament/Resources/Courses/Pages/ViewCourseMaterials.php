<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\Schemas\CourseMaterialsInfolist;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseMaterials extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected static ?string $maxWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Course Details')
                ->icon('heroicon-m-arrow-left')
                ->url(fn(): string => route('filament.admin.resources.courses.view', $this->record)),
            EditAction::make(),
        ];
    }

    public function infolist(\Filament\Schemas\Schema $infolist): \Filament\Schemas\Schema
    {
        return CourseMaterialsInfolist::configure($infolist);
    }

    public function getTitle(): string
    {
        return 'Course Materials: ' . $this->record->title;
    }
}
