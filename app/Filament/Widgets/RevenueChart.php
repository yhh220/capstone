<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Chart';

    protected function getData(): array
    {
        // Currently empty data (RM 0) until frontend syncs orders
        return [
            'datasets' => [
                [
                    'label' => 'Monthly Revenue (RM)',
                    'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    'borderColor' => '#ec4899', // Pinkish/Neon color
                    'backgroundColor' => 'rgba(236, 72, 153, 0.2)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
