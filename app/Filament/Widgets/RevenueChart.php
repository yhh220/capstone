<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 7,
    ];

    protected ?string $heading = 'Monthly Revenue';

    protected ?string $description = 'Completed order revenue over the past 12 months (RM)';

    protected function getData(): array
    {
        $months  = [];
        $revenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $revenue[] = (float) Order::where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (RM)',
                    'data'            => $revenue,
                    'borderColor'     => '#ec4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.12)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointRadius'     => 4,
                    'pointHoverRadius'=> 6,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => [
                        'callback' => 'function(v){ return "RM " + v.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}
