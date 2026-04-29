<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Orders')
            ->description('Latest orders placed on the store')
            ->query(Order::query()->latest()->limit(8))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->icon('heroicon-m-user'),

                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('MYR')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'processing' => 'info',
                        'shipped'    => 'primary',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending'    => 'heroicon-m-clock',
                        'processing' => 'heroicon-m-cog-6-tooth',
                        'shipped'    => 'heroicon-m-truck',
                        'delivered'  => 'heroicon-m-check-circle',
                        'cancelled'  => 'heroicon-m-x-circle',
                        default      => 'heroicon-m-question-mark-circle',
                    }),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'pending' => 'warning',
                        'failed'  => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Placed')
                    ->since()
                    ->sortable()
                    ->color('gray'),
            ])
            ->paginated(false)
            ->emptyStateHeading('No orders yet')
            ->emptyStateDescription('Orders placed on the store will appear here.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}
