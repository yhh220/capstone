<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategoryDistributionChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 5,
    ];

    protected ?string $heading = 'Products by Category';

    protected ?string $description = 'Distribution of active products across categories';

    protected function getData(): array
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])->orderByDesc('products_count')->get()
          ->filter(fn ($c) => $c->products_count > 0);

        // If no category has products yet, show all with 0
        if ($categories->isEmpty()) {
            $categories = Category::orderBy('name')->get()->map(function ($c) {
                $c->products_count = 0;
                return $c;
            });
        }

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data'  => $categories->pluck('products_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(236, 72, 153, 0.85)',   // Rose/Pink
                        'rgba(139, 92, 246, 0.85)',   // Violet
                        'rgba(59, 130, 246, 0.85)',   // Blue
                        'rgba(16, 185, 129, 0.85)',   // Emerald
                        'rgba(245, 158, 11, 0.85)',   // Amber
                        'rgba(239, 68, 68, 0.85)',    // Red
                        'rgba(20, 184, 166, 0.85)',   // Teal
                        'rgba(249, 115, 22, 0.85)',   // Orange
                    ],
                    'borderWidth' => 2,
                    'borderColor' => 'rgba(255,255,255,0.15)',
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => ['padding' => 16],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
