<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 7,
    ];

    protected ?string $heading = 'Revenue';

    protected ?string $description = 'Completed order revenue (RM)';

    public ?string $filter = '12';

    protected function getFilters(): ?array
    {
        return [
            '3'  => 'Last 3 months',
            '6'  => 'Last 6 months',
            '12' => 'Last 12 months',
        ];
    }

    protected function getData(): array
    {
        $monthCount = (int) ($this->filter ?? 12);
        $start = Carbon::now()->startOfMonth()->subMonths($monthCount - 1);

        // One aggregated query instead of one query per month.
        $totals = Order::where('status', 'completed')
            ->where('created_at', '>=', $start)
            ->get(['created_at', 'total_amount'])
            ->groupBy(fn (Order $o) => $o->created_at->format('Y-m'))
            ->map(fn ($orders) => (float) $orders->sum('total_amount'));

        $months  = [];
        $revenue = [];
        for ($i = $monthCount - 1; $i >= 0; $i--) {
            $date      = Carbon::now()->subMonths($i);
            $months[]  = $date->format('M Y');
            $revenue[] = $totals[$date->format('Y-m')] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label'            => 'Revenue (RM)',
                    'data'             => $revenue,
                    'borderColor'      => '#C8413D',
                    'backgroundColor'  => 'rgba(200, 65, 61, 0.12)',
                    'fill'             => true,
                    'tension'          => 0.4,
                    'pointRadius'      => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs
    {
        // RawJs is required for JS callbacks — a plain string would be
        // JSON-encoded and silently ignored by Chart.js.
        return RawJs::make(<<<'JS'
            {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => 'RM ' + ctx.parsed.y.toLocaleString(),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => 'RM ' + v.toLocaleString(),
                        },
                    },
                },
            }
        JS);
    }
}
