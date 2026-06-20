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
        'default' => 'full',
        'xl'      => 7,
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

        // One aggregated query, grouped in SQL rather than pulling every row into
        // PHP. Month-bucketing syntax differs by driver (MySQL vs SQLite, the
        // latter used in tests/local dev), so branch on the connection.
        $ymExpr = match (Order::query()->getConnection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            default  => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $totals = Order::where('status', 'delivered')
            ->whereNull('refunded_at') // defense-in-depth: never count a refunded order as revenue
            ->where('created_at', '>=', $start)
            ->selectRaw("{$ymExpr} as ym, SUM(total_amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->map(fn ($v) => (float) $v);

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
                    'pointRadius'      => 3,
                    'pointHoverRadius' => 5,
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
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 6,
                        },
                    },
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
