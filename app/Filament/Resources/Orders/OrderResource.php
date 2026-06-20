<?php

namespace App\Filament\Resources\Orders;

use App\Mail\OrderCancelledMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderRefundProcessedMail;
use App\Mail\OrderShippedMail;
use App\Mail\OwnerAlertMail;
use App\Models\Order;
use App\Services\RefundCalculator;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;
    protected static \UnitEnum|string|null $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 1;

    /** Badge = paid/confirmed orders still awaiting fulfilment (same idea as Bookings). */
    public static function getNavigationBadge(): ?string
    {
        return (string) Order::where('status', 'processing')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Mirrors NotifiesOwner's notifyOwner() (used by the customer-facing Livewire
     * forms) — duplicated here rather than traited in, since this is a static
     * Filament Resource action closure, not a Livewire component instance. Shares
     * the same rate-limiter key/budget as booking/enquiry/customer-cancel alerts.
     */
    private static function notifyOwnerOfCancellation(Order $order, string $cancelledBy): void
    {
        $email = config('services.store.email');

        if (! $email) {
            return;
        }

        RateLimiter::attempt(
            'owner-alert-email',
            maxAttempts: 10,
            callback: function () use ($email, $order, $cancelledBy) {
                Mail::to($email)->send(new OwnerAlertMail(
                    'Order cancelled',
                    [
                        'Order Number' => $order->order_number,
                        'Customer'     => $order->customer_name,
                        'Cancelled By' => $cancelledBy,
                        'Reason'       => $order->cancellation_reason,
                        'Refund'       => $order->refund_amount !== null
                            ? 'RM ' . number_format($order->refund_amount, 2) . ' (' . $order->refund_percentage . '%)'
                            : null,
                    ],
                    url('/admin/orders/' . $order->getKey() . '/edit'),
                ));
            },
            decaySeconds: 3600,
        );
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Information')->schema([
                Forms\Components\TextInput::make('order_number')->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                    ])
                    ->required(),
            ])->columns(['default' => 1, 'sm' => 2]),

            Section::make('Customer Details')->schema([
                Forms\Components\TextInput::make('customer_name')->disabled(),
                Forms\Components\TextInput::make('customer_email')->disabled(),
                Forms\Components\TextInput::make('customer_phone')->disabled(),
                Forms\Components\TextInput::make('total_amount')
                    ->prefix('RM')
                    ->disabled(),
            ])->columns(['default' => 1, 'sm' => 2]),

            Section::make('Order Items')->schema([
                Forms\Components\Placeholder::make('items_list')
                    ->label('')
                    ->content(function ($record): \Illuminate\Support\HtmlString {
                        if (! $record) {
                            return new \Illuminate\Support\HtmlString('<p class="text-sm text-gray-400">Save the order first to see items.</p>');
                        }
                        $items = $record->items()->with('product')->get();
                        if ($items->isEmpty()) {
                            return new \Illuminate\Support\HtmlString('<p class="text-sm text-gray-400">No items found.</p>');
                        }
                        // Inline styles (not Tailwind classes) — the Filament admin
                        // CSS bundle doesn't ship the app's utility classes, so class
                        // names here render unstyled (columns collapse together).
                        $cell = 'padding:8px 12px;font-size:13px;border-bottom:1px solid rgba(128,128,128,0.15);white-space:nowrap;';
                        $rows = $items->map(fn ($item) =>
                            "<tr>
                                <td style='padding:8px 16px 8px 0;font-size:13px;border-bottom:1px solid rgba(128,128,128,0.15);white-space:normal;'>" . e($item->product_name) . "</td>
                                <td style='{$cell}text-align:center;'>{$item->quantity}</td>
                                <td style='{$cell}text-align:right;'>RM " . number_format($item->unit_price, 2) . "</td>
                                <td style='{$cell}padding-right:0;text-align:right;font-weight:600;'>RM " . number_format($item->subtotal, 2) . "</td>
                            </tr>"
                        )->implode('');
                        $th = 'padding:0 12px 8px;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#9ca3af;border-bottom:2px solid rgba(128,128,128,0.25);';
                        return new \Illuminate\Support\HtmlString("
                            <div style='overflow-x:auto;'>
                            <table style='width:100%;border-collapse:collapse;min-width:420px;'>
                                <thead><tr>
                                    <th style='{$th}padding-left:0;text-align:left;'>Product</th>
                                    <th style='{$th}text-align:center;'>Qty</th>
                                    <th style='{$th}text-align:right;'>Unit Price</th>
                                    <th style='{$th}padding-right:0;text-align:right;'>Subtotal</th>
                                </tr></thead>
                                <tbody>{$rows}</tbody>
                            </table>
                            </div>
                        ");
                    }),
            ])->columnSpanFull()->visibleOn('edit'),

            Section::make('Order Notes')->schema([
                Forms\Components\Textarea::make('notes')
                    ->label('Customer Notes')
                    ->placeholder('No notes from customer.')
                    ->disabled()
                    ->columnSpanFull(),
            ])->visibleOn('edit')->collapsed(fn ($record) => blank($record?->notes)),

            Section::make('Cancellation Details')->schema([
                Forms\Components\TextInput::make('cancelled_by')
                    ->label('Cancelled by')
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'unknown'))
                    ->disabled(),
                Forms\Components\TextInput::make('cancellation_reason')
                    ->label('Reason')
                    ->disabled(),
                Forms\Components\TextInput::make('refund_percentage')
                    ->label('Refund %')
                    ->suffix('%')
                    ->disabled(),
                Forms\Components\TextInput::make('refund_amount')
                    ->label('Refund amount')
                    ->prefix('RM')
                    ->disabled(),
                Forms\Components\TextInput::make('refunded_at')
                    ->label('Refund sent on')
                    ->formatStateUsing(fn (?string $state): string => $state ? \Illuminate\Support\Carbon::parse($state)->format('d M Y, h:i A') : 'Not yet sent')
                    ->disabled(),
            ])->columns(['default' => 1, 'sm' => 2])
              ->visible(fn (?Order $record) => $record?->status === 'cancelled')
              ->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->tooltip('Unique order reference number'),
                TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('MYR', locale: 'ms_MY')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning'   => 'pending',
                        'info'      => 'processing',
                        'primary'   => 'shipped',
                        'success'   => 'delivered',
                        'danger'    => 'cancelled',
                    ])
                    ->tooltip(fn (string $state): string => 'Order status is ' . $state),
                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                    ])
                    ->tooltip(fn (string $state): string => 'Payment is ' . $state),
                TextColumn::make('refund_status')
                    ->label('Refund if cancelled')
                    ->toggleable()
                    ->state(function (Order $record): string {
                        if ($record->status === 'cancelled') {
                            if ($record->refund_amount === null) {
                                return 'Nothing to refund';
                            }
                            return $record->refunded_at !== null
                                ? 'Sent RM ' . number_format($record->refund_amount, 2)
                                : 'Pending RM ' . number_format($record->refund_amount, 2);
                        }
                        return (new RefundCalculator())->eligibilityLabel($record);
                    })
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Payment')
                    ->multiple()
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                    ]),
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->schema([
                        Forms\Components\DatePicker::make('from')->label('Ordered from'),
                        Forms\Components\DatePicker::make('until')->label('Ordered until'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) $indicators[] = 'From ' . $data['from'];
                        if ($data['until'] ?? null) $indicators[] = 'Until ' . $data['until'];
                        return $indicators;
                    }),
            ])
            ->persistFiltersInSession()
            ->recordActions([
                EditAction::make()
                    ->tooltip('View/edit order details'),
                Action::make('advance')
                    ->label('Advance')
                    ->icon(Heroicon::OutlinedArrowRightCircle)
                    ->color('success')
                    ->tooltip(fn (Order $record): string => "Advance status to " . ucfirst($record->next_status ?? ''))
                    ->visible(fn(Order $record) => $record->next_status !== null)
                    ->requiresConfirmation()
                    ->modalHeading(fn(Order $record) => "Advance to " . ucfirst($record->next_status ?? '') . "?")
                    ->action(function (Order $record) {
                        if ($record->next_status) {
                            $record->update(['status' => $record->next_status]);
                        }
                    }),
                Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->tooltip('Mark order as fully paid')
                    ->visible(fn (Order $record) => $record->payment_status === 'pending' && $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->modalHeading('Mark this order as paid?')
                    ->modalDescription('The customer will get the same confirmation email as the online payment flow.')
                    ->action(function (Order $record): void {
                        $record->update([
                            'payment_status' => 'paid',
                            'status'         => $record->status === 'pending' ? 'processing' : $record->status,
                            'expires_at'     => null,
                        ]);

                        // Online payment (PaymentPage::pay()) emails the customer on
                        // success; this manual path (e.g. reconciling a bank transfer)
                        // skipped that entirely, so the customer never heard back.
                        try {
                            Mail::to($record->customer_email)->send(new OrderConfirmationMail($record->fresh('items')));
                            Notification::make()->title('Marked paid — customer notified')->success()->send();
                        } catch (\Throwable $e) {
                            logger()->error('Mark-paid confirmation email failed: ' . $e->getMessage());
                            Notification::make()->title('Marked paid, but the email failed to send')->warning()->send();
                        }
                    }),
                Action::make('markShipped')
                    ->label('Mark Shipped')
                    ->icon(Heroicon::OutlinedTruck)
                    ->color('primary')
                    ->tooltip('Mark order as shipped and enter tracking number')
                    ->visible(fn (Order $record) => in_array($record->status, ['processing'], true))
                    ->schema([
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Tracking number')
                            ->required()
                            ->maxLength(100)
                            ->default(fn (Order $record) => $record->tracking_number)
                            ->helperText('Shown to the customer in the "shipped" email.'),
                    ])
                    ->modalHeading('Mark as shipped & notify customer')
                    ->modalSubmitActionLabel('Mark shipped & send email')
                    ->action(function (Order $record, array $data): void {
                        $record->update([
                            'status'          => 'shipped',
                            'tracking_number' => $data['tracking_number'],
                        ]);

                        try {
                            Mail::to($record->customer_email)->send(new OrderShippedMail($record->fresh()));
                            Notification::make()->title('Marked shipped — customer notified')->success()->send();
                        } catch (\Throwable $e) {
                            logger()->error('Shipped email failed: ' . $e->getMessage());
                            Notification::make()->title('Marked shipped, but the email failed to send')->warning()->send();
                        }
                    }),
                Action::make('invoice')
                    ->label('Invoice')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('gray')
                    ->tooltip('View or print invoice')
                    ->url(fn (Order $record) => route('invoice.show', $record->order_number))
                    ->openUrlInNewTab(),
                Action::make('resendConfirmation')
                    ->label('Resend confirmation')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->color('gray')
                    ->tooltip('Resend the order confirmation email to the customer')
                    ->visible(fn (Order $record) => filled($record->customer_email) && $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->modalHeading('Resend the order confirmation email?')
                    ->action(function (Order $record): void {
                        try {
                            Mail::to($record->customer_email)->send(new OrderConfirmationMail($record->fresh('items')));
                            Notification::make()->title('Confirmation email resent')->success()->send();
                        } catch (\Throwable $e) {
                            logger()->error('Resend confirmation failed: ' . $e->getMessage());
                            Notification::make()->title('Failed to resend the email')->danger()->send();
                        }
                    }),
                Action::make('cancelOrder')
                    ->label('Cancel & restock')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->tooltip('Cancel this order and restock its items')
                    // Only before the goods leave the warehouse — shipped/delivered
                    // orders return stock through a manual returns process instead.
                    ->visible(fn (Order $record) => in_array($record->status, ['pending', 'processing'], true))
                    ->requiresConfirmation()
                    ->modalHeading('Cancel this order and return its stock?')
                    ->modalDescription(function (Order $record): string {
                        if ($record->payment_status !== 'paid') {
                            return 'The items go back into inventory and the order is marked cancelled. No payment was taken, so no refund applies.';
                        }
                        $refund = (new RefundCalculator())->calculate($record);
                        return $refund
                            ? 'The items go back into inventory and the order is marked cancelled. Recorded refund: RM ' . number_format($refund['amount'], 2) . ' (' . $refund['percentage'] . '%).'
                            : 'The items go back into inventory and the order is marked cancelled.';
                    })
                    ->action(function (Order $record): void {
                        $calculator = new RefundCalculator();

                        $order = DB::transaction(function () use ($record, $calculator) {
                            $order = Order::where('id', $record->id)->lockForUpdate()->with('items')->first();
                            if (! $order || $order->status === 'cancelled') {
                                return null;
                            }

                            $refund = $order->payment_status === 'paid' ? $calculator->calculate($order) : null;

                            $order->restockItems();
                            $order->update([
                                'status'              => 'cancelled', // event stamps cancelled_at
                                'cancelled_by'        => 'admin',
                                'cancellation_reason' => 'Cancelled by admin',
                                'refund_amount'       => $refund['amount'] ?? null,
                                'refund_percentage'   => $refund['percentage'] ?? null,
                            ]);

                            return $order;
                        });

                        if ($order === null) {
                            Notification::make()->title('Order was already cancelled')->warning()->send();
                            return;
                        }

                        $order = $order->fresh('items');

                        try {
                            Mail::to($order->customer_email)->send(new OrderCancelledMail($order));
                        } catch (\Throwable $e) {
                            logger()->error('Order cancellation email failed: ' . $e->getMessage());
                        }

                        // Both sides get an email on every cancellation, regardless of who
                        // triggered it — keeps the shop inbox a complete audit trail and
                        // avoids a separate "only notify if customer-initiated" branch.
                        static::notifyOwnerOfCancellation($order, 'Admin');

                        Notification::make()->title('Order cancelled & stock returned')->success()->send();
                    }),
                Action::make('markRefunded')
                    ->label('Mark refund as sent')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->color('success')
                    ->tooltip('Confirm the recorded refund has actually been sent to the customer')
                    ->visible(fn (Order $record) => $record->status === 'cancelled' && $record->refund_amount !== null && $record->refunded_at === null)
                    ->requiresConfirmation()
                    ->modalHeading('Mark this refund as sent?')
                    ->modalDescription(fn (Order $record) => 'Confirms RM ' . number_format($record->refund_amount, 2) . ' has actually been transferred to the customer (e.g. bank transfer/e-wallet outside this system) and emails them that confirmation.')
                    ->action(function (Order $record): void {
                        $record->update(['refunded_at' => now()]);

                        try {
                            Mail::to($record->customer_email)->send(new OrderRefundProcessedMail($record->fresh()));
                            Notification::make()->title('Refund marked as sent — customer notified')->success()->send();
                        } catch (\Throwable $e) {
                            logger()->error('Refund-sent email failed: ' . $e->getMessage());
                            Notification::make()->title('Marked as sent, but the email failed to send')->warning()->send();
                        }
                    }),
                DeleteAction::make()
                    ->tooltip('Delete order record')
                    // pending/processing still hold reserved stock (decremented at
                    // checkout, not yet returned). Deleting straight past "Cancel &
                    // restock" would silently leak that stock with no way back.
                    ->visible(fn (Order $record) => ! in_array($record->status, ['pending', 'processing'], true)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            [$protected, $deletable] = $records->partition(
                                fn (Order $order) => in_array($order->status, ['pending', 'processing'], true)
                            );

                            $deletable->each(fn (Order $order) => $order->delete());

                            if ($protected->isEmpty()) {
                                Notification::make()->title('Deleted ' . $deletable->count() . ' order(s)')->success()->send();
                                return;
                            }

                            Notification::make()
                                ->title('Deleted ' . $deletable->count() . ' order(s)')
                                ->body($protected->count() . ' order(s) skipped — "Cancel & restock" first, their stock is still reserved.')
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
