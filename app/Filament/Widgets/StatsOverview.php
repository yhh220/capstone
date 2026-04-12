<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendingBookings = Booking::where('status', 'pending')->count();
        $todayBookings   = Booking::whereDate('preferred_date', today())->count();
        $unreadContacts  = Contact::where('is_read', false)->count();

        return [
            Stat::make('Active Products', Product::where('is_active', true)->count())
                ->description('Products in catalogue')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),

            Stat::make('Bookings', Booking::count())
                ->description($pendingBookings . ' pending')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($pendingBookings > 0 ? 'warning' : 'success'),

            Stat::make("Today's Appointments", $todayBookings)
                ->description('Bookings scheduled today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Unread Enquiries', $unreadContacts)
                ->description('Contact form messages')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($unreadContacts > 0 ? 'danger' : 'success'),

            Stat::make('Total Revenue', 'RM ' . number_format(Order::where('status', 'completed')->sum('total_amount'), 2))
                ->description('Completed sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
