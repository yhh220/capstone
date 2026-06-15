<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\ChatLog;
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
        // Cache the dashboard aggregates for a minute — these count/sum queries
        // ran on every dashboard visit and every Livewire poll.
        $s = Cache::remember('dashboard_stats', 60, fn () => [
            'activeProducts'  => Product::where('is_active', true)->count(),
            'totalBookings'   => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'todayBookings'   => Booking::whereDate('preferred_date', today())->count(),
            'unreadContacts'  => Contact::where('is_read', false)->count(),
            'registeredUsers' => User::where('role', 'client')->count(),
            'totalOrders'     => Order::count(),
            'pendingOrders'   => Order::where('status', 'pending')->count(),
            'monthRevenue'    => (float) Order::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            // Scope chat stats to the current month so spam / nonsense from the
            // past never inflates the number — it resets monthly and reflects
            // current activity. Old rows are pruned by the scheduler anyway.
            'chatTotal'       => ChatLog::where('feature', 'chat')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'chatFallback'    => ChatLog::where('feature', 'chat')
                ->where('status', 'fallback')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ]);

        return [
            Stat::make('Active Products', $s['activeProducts'])
                ->description('Products in catalogue')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),

            Stat::make('Total Bookings', $s['totalBookings'])
                ->description($s['pendingBookings'] . ' awaiting confirmation')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($s['pendingBookings'] > 0 ? 'warning' : 'gray'),

            Stat::make("Today's Appointments", $s['todayBookings'])
                ->description('Bookings scheduled today')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Unread Enquiries', $s['unreadContacts'])
                ->description('Contact form messages')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($s['unreadContacts'] > 0 ? 'danger' : 'success'),

            Stat::make('Chatbot Questions', $s['chatTotal'])
                ->description($s['chatFallback'] > 0
                    ? $s['chatFallback'] . ' unanswered this month'
                    : 'All answered this month')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($s['chatFallback'] > 0 ? 'warning' : 'success'),

            Stat::make('Registered Users', $s['registeredUsers'])
                ->description('Customer accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Orders', $s['totalOrders'])
                ->description($s['pendingOrders'] . ' awaiting processing')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($s['pendingOrders'] > 0 ? 'warning' : 'gray'),

            Stat::make('Revenue This Month', 'RM ' . number_format($s['monthRevenue'], 2))
                ->description('Completed orders this month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
