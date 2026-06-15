<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // Cache the dashboard aggregates for a minute — these 11 count/sum
        // queries ran on every dashboard visit and every Livewire poll.
        $s = Cache::remember('dashboard_stats', 60, fn () => [
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'todayBookings'   => Booking::whereDate('preferred_date', today())->count(),
            'unreadContacts'  => Contact::where('is_read', false)->count(),
            'totalOrders'     => Order::count(),
            'monthRevenue'    => (float) Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'registeredUsers' => User::where('role', 'client')->count(),
            'activeProducts'  => Product::where('is_active', true)->count(),
            'totalBookings'   => Booking::count(),
            'totalRevenue'    => (float) Order::where('status', 'completed')->sum('total_amount'),
        ]);

        $pendingBookings = $s['pendingBookings'];
        $todayBookings   = $s['todayBookings'];
        $unreadContacts  = $s['unreadContacts'];
        $totalOrders     = $s['totalOrders'];
        $monthRevenue    = $s['monthRevenue'];
        $registeredUsers = $s['registeredUsers'];

        return [
            Stat::make('Active Products', $s['activeProducts'])
                ->description('Products in catalogue')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->chart([4, 7, 5, 9, 12, 10, 12])
                ->color('info'),

            Stat::make('Total Orders', $totalOrders)
                ->description($pendingBookings . ' bookings pending')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart([0, 0, 0, 0, 0, 0, $totalOrders])
                ->color($totalOrders > 0 ? 'warning' : 'gray'),

            Stat::make('Unread Enquiries', $unreadContacts)
                ->description('Contact form messages')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($unreadContacts > 0 ? 'danger' : 'success'),

            Stat::make('Revenue This Month', 'RM ' . number_format($monthRevenue, 2))
                ->description('Completed orders revenue')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([0, 0, 0, 0, 0, 0, $monthRevenue > 0 ? $monthRevenue : 0])
                ->color('success'),

            Stat::make("Today's Appointments", $todayBookings)
                ->description('Bookings scheduled today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Total Bookings', $s['totalBookings'])
                ->description('All time bookings')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Stat::make('Registered Users', $registeredUsers)
                ->description('Customer accounts')
                ->descriptionIcon('heroicon-m-users')
                ->chart([1, 1, 1, 1, 1, 1, $registeredUsers])
                ->color('info'),

            Stat::make('Total Revenue', 'RM ' . number_format($s['totalRevenue'], 2))
                ->description('All completed sales')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
