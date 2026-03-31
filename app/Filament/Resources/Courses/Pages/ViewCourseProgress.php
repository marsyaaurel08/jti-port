<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Enrollment;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ViewCourseProgress extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CourseResource::class;

    public $record;

    public function getView(): string
    {
        return 'filament.resources.courses.pages.view-course-progress';
    }

    public function mount($record): void
    {
        $this->record = CourseResource::getModel()::findOrFail($record);
    }

    public function getTitle(): string
    {
        return 'Progress Peserta: ' . $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Detail Kursus')
                ->url(fn(): string => route('filament.admin.resources.courses.view', $this->record))
                ->icon('heroicon-m-arrow-left'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return $this->record->enrollments()
            ->with(['user', 'course', 'lastAccessedLesson'])
            ->getQuery();
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\Courses\Tables\CourseProgressTable::configure($table);
    }
}
