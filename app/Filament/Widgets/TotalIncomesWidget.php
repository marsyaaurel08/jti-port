<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Carbon;

class TotalIncomesWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 2;

    protected function getStats(): array
    {
        $filterMonth = $this->pageFilters['filterMonth'] ?? now()->format('Y-m');
        $selectedDate = Carbon::parse($filterMonth . '-01');

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $selectedDate->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $selectedDate->copy()->subMonths($i)->endOfMonth();

            $chartData[] = \App\Models\Order::query()
                ->where('status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
        }

        $currentMonth = $chartData[5];
        $previousMonth = $chartData[4];
        $difference = $currentMonth - $previousMonth;

        if ($difference > 0) {
            $description = 'Rp ' . number_format($difference, 0, ',', '.') . ' peningkatan dari bulan lalu';
            $icon = 'heroicon-m-arrow-trending-up';
            $color = 'success';
        } elseif ($difference < 0) {
            $description = 'Rp ' . number_format(abs($difference), 0, ',', '.') . ' penurunan dari bulan lalu';
            $icon = 'heroicon-m-arrow-trending-down';
            $color = 'danger';
        } else {
            $description = 'Tidak ada perubahan dari bulan lalu';
            $icon = 'heroicon-m-minus';
            $color = 'gray';
        }

        return [
            StatsOverviewWidget\Stat::make(
                label: 'Total Pendapatan',
                value: 'Rp ' . number_format($currentMonth, 0, ',', '.')
            )
                ->description($description)
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($chartData)
                ->columnSpanFull(),
        ];
    }
}
