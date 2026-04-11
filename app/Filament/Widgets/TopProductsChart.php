<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected ?string $heading = 'Top Products Chart';

    protected function getData(): array
    {
        // Currently empty data (0 items sold) until frontend syncs
        return [
            'datasets' => [
                [
                    'label' => 'Units Sold (Top Sales)',
                    'data' => [0, 0, 0, 0, 0],
                    'backgroundColor' => [
                        'rgba(139, 92, 246, 0.8)', // Violet
                        'rgba(236, 72, 153, 0.8)', // Pink/Neon
                        'rgba(59, 130, 246, 0.8)', // Blue
                        'rgba(16, 185, 129, 0.8)', // Emerald
                        'rgba(245, 158, 11, 0.8)', // Amber
                    ],
                ],
            ],
            // Dummy best sellers, showing 0 sales.
            'labels' => ['Pioneer Head Unit', 'Dash Camera 4K', 'Seat Covers Set', 'LED Headlight', 'Subwoofer 10"'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
