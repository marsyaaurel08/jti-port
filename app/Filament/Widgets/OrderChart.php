<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class OrderChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Statistik Pesanan (Bar Chart)';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 3;

    protected function getData(): array
    {
        $filterMonth = $this->pageFilters['filterMonth'] ?? now()->format('Y-m');
        $selectedDate = Carbon::parse($filterMonth . '-01');

        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = $selectedDate->copy()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');

            $data[] = \App\Models\Order::query()
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pesanan',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6', // Tailwind blue
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
