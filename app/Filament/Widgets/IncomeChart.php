<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class IncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Trend Pendapatan (Line Chart)';

    protected static ?int $sort = 2; // Biar tampil setelah stats overview

    protected int | string | array $columnSpan = 3;

    protected function getData(): array
    {
        $filterMonth = $this->pageFilters['filterMonth'] ?? now()->format('Y-m');
        $selectedDate = Carbon::parse($filterMonth . '-01');

        $data = [];
        $labels = [];

        // Loop 12 bulan terakhir dari bulan yang dipilih
        for ($i = 11; $i >= 0; $i--) {
            $month = $selectedDate->copy()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');

            $data[] = \App\Models\Order::query()
                ->where('status', 'paid')
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('total_amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#10b981', // Tailwind success green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
