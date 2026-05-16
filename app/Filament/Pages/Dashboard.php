<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IncomeChart;
use App\Filament\Widgets\LatestPendingOrders;
use App\Filament\Widgets\OrderChart;
use App\Filament\Widgets\TotalIncomesWidget;
use App\Filament\Widgets\TotalOrdersWidget;
use App\Filament\Widgets\TotalUsersWidget;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    // Tambahkan baris berikut:
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 6,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->schema([
                    Select::make('filterMonth')
                        ->label('Pilih Bulan')
                        ->options(function () {
                            $months = [];
                            for ($i = 0; $i < 12; $i++) {
                                $date = now()->subMonths($i);
                                $months[$date->format('Y-m')] = $date->translatedFormat('F Y');
                            }

                            return $months;
                        })
                        ->default(now()->format('Y-m'))
                        ->searchable(),
                ]),
        ];
    }

    public function getWidgets(): array
    {
        return [
            TotalUsersWidget::class,
            TotalIncomesWidget::class,
            TotalOrdersWidget::class,
            IncomeChart::class,
            OrderChart::class,
            LatestPendingOrders::class,
        ];
    }
}
