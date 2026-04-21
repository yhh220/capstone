<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopProductsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 12,
        ];
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RevenueChart::class,
            TopProductsChart::class,
            RecentActivityWidget::class,
        ];
    }
}
