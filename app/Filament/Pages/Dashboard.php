<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CategoryDistributionChart;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopProductsChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'sm'      => 2,
            'md'      => 4,
            'xl'      => 12,
        ];
    }

    public function getWidgets(): array
    {
        return [
            // Row 1: 4 KPI Cards (Z-Pattern top)
            StatsOverview::class,

            // Row 2: Revenue Line Chart (wide) + Category Doughnut (narrow)
            RevenueChart::class,
            CategoryDistributionChart::class,

            // Row 3: Product Stock Bar Chart (full width)
            TopProductsChart::class,

            // Row 4: Recent Orders Table (full width)
            RecentOrdersWidget::class,

            // Row 5: Recent Activity Log (full width)
            RecentActivityWidget::class,
        ];
    }
}
