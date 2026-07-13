<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->isStaffMember() ?? false;
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Inventory Watchlist';

    protected ?string $description = 'The 10 active products with the lowest stock, including out-of-stock items';

    protected function getData(): array
    {
        $products = Product::where('is_active', true)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(10)
            ->get(['name', 'stock', 'category_id']);

        return [
            'datasets' => [
                [
                    'label'           => 'Stock Available',
                    'data'            => $products->pluck('stock')->toArray(),
                    'backgroundColor' => $products->map(fn ($product) => match (true) {
                        $product->stock <= 0 => 'rgba(239, 68, 68, 0.85)',
                        $product->stock < 5 => 'rgba(245, 158, 11, 0.85)',
                        default => 'rgba(59, 130, 246, 0.85)',
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
