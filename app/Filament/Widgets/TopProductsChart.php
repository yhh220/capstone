<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Product Stock Overview';

    protected ?string $description = 'Current stock levels for all active products';

    protected function getData(): array
    {
        $products = Product::where('is_active', true)
            ->orderByDesc('stock')
            ->limit(10)
            ->get(['name', 'stock', 'category_id']);

        return [
            'datasets' => [
                [
                    'label'           => 'Stock Available',
                    'data'            => $products->pluck('stock')->toArray(),
                    'backgroundColor' => $products->map(function ($p, $i) {
                        $colors = [
                            'rgba(236, 72, 153, 0.85)',
                            'rgba(139, 92, 246, 0.85)',
                            'rgba(59, 130, 246, 0.85)',
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(245, 158, 11, 0.85)',
                            'rgba(239, 68, 68, 0.85)',
                            'rgba(20, 184, 166, 0.85)',
                            'rgba(249, 115, 22, 0.85)',
                            'rgba(99, 102, 241, 0.85)',
                            'rgba(34, 197, 94, 0.85)',
                        ];
                        return $colors[$i % count($colors)];
                    })->toArray(),
                    'borderRadius'      => 6,
                    'borderSkipped'     => false,
                ],
            ],
            'labels' => $products->map(fn ($p) => strlen($p->name) > 25 ? substr($p->name, 0, 22) . '…' : $p->name)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 0,
                        'autoSkip'    => true,
                        'font'        => ['size' => 11],
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text'    => 'Units in Stock',
                    ],
                ],
            ],
        ];
    }
}
