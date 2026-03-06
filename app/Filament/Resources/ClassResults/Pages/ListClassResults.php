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
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-m-list-bullet'),

            'upcoming' => Tab::make('Belum Dimulai')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('start_date', '>', now())
                )
                ->icon('heroicon-m-calendar-days')
                ->badge(fn() => $this->getModel()::where('start_date', '>', now())->count())
                ->badgeColor('info'),

            'ongoing' => Tab::make('Sedang Berjalan')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                )
                ->icon('heroicon-m-play-circle')
                ->badge(
                    fn() => $this->getModel()::where('start_date', '<=', now())
                        ->where('end_date', '>=', now())->count()
                )
                ->badgeColor('success'),

            'finished' => Tab::make('Sudah Selesai')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('end_date', '<', now())
                )
                ->icon('heroicon-m-check-badge')
                ->badge(fn() => $this->getModel()::where('end_date', '<', now())->count())
                ->badgeColor('gray'),
        ];
    }
}
