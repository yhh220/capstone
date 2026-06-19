<?php

namespace App\Filament\Resources\Orders;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
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
    protected static \UnitEnum|string|null $navigationGroup = 'Shop';
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
                    ->color('primary'),
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
                    ]),
                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                    ]),
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
                EditAction::make(),
                Action::make('advance')
                    ->label('Advance')
                    ->icon(Heroicon::OutlinedArrowRightCircle)
                    ->color('success')
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
                    ->visible(fn (Order $record) => $record->payment_status === 'pending' && $record->status !== 'cancelled')
                    ->requiresConfirmation()
                    ->modalHeading('Mark this order as paid?')
                    ->action(fn (Order $record) => $record->update([
                        'payment_status' => 'paid',
                        'status'         => $record->status === 'pending' ? 'processing' : $record->status,
                        'expires_at'     => null,
                    ])),
                Action::make('markShipped')
                    ->label('Mark Shipped')
                    ->icon(Heroicon::OutlinedTruck)
                    ->color('primary')
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
                    ->url(fn (Order $record) => route('invoice.show', $record->order_number))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
