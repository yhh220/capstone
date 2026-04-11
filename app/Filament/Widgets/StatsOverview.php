<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Revenue', 'RM ' . number_format(Order::where('status', 'completed')->sum('total_amount'), 2))
                ->description('Total completed sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Sparkline
                ->color('success'),
            
            Stat::make('Total Products', Product::count())
                ->description('Active products in catalog')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            
            Stat::make('Total Orders', Order::count())
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([1, 4, 3, 8, 5, 2, 10]) // Sparkline
                ->color('warning'),
        ];
    }
}
