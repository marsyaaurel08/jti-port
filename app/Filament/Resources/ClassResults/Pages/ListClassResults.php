<?php

namespace App\Filament\Resources\ClassResults\Pages;

use App\Filament\Resources\ClassResults\ClassResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListClassResults extends ListRecords
{
    protected static string $resource = ClassResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
    public function getDefaultActiveTab(): string | int | null
    {
        return 'ongoing';
    }
    public function getTabs(): array
    {
        $user = auth()->user();
        $isAdmin = $user->hasAnyRole(['admin', 'super_admin']);
        $instructorId = $user->id;

        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-m-list-bullet')
                ->modifyQueryUsing(function (Builder $query) use ($isAdmin, $instructorId) {
                    return $query->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId));
                }),

            'upcoming' => Tab::make('Belum Dimulai')
                ->modifyQueryUsing(function (Builder $query) use ($isAdmin, $instructorId) {
                    return $query->where('start_date', '>', now())
                        ->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId));
                })
                ->icon('heroicon-m-calendar-days')
                ->badge(fn() => $this->getModel()::where('start_date', '>', now())
                    ->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId))
                    ->count())
                ->badgeColor('info'),

            'ongoing' => Tab::make('Sedang Berjalan')
                ->modifyQueryUsing(function (Builder $query) use ($isAdmin, $instructorId) {
                    return $query->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId));
                })
                ->icon('heroicon-m-play-circle')
                ->badge(fn() => $this->getModel()::where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId))
                    ->count())
                ->badgeColor('success'),

            'finished' => Tab::make('Sudah Selesai')
                ->modifyQueryUsing(function (Builder $query) use ($isAdmin, $instructorId) {
                    return $query->where('end_date', '<', now())
                        ->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId));
                })
                ->icon('heroicon-m-check-badge')
                ->badge(fn() => $this->getModel()::where('end_date', '<', now())
                    ->when(!$isAdmin, fn($q) => $q->where('instructor_id', $instructorId))
                    ->count())
                ->badgeColor('gray'),
        ];
    }
}
