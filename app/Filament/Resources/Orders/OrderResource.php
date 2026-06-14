<?php

namespace App\Filament\Resources\Orders;

use App\Models\Order;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;
    protected static \UnitEnum|string|null $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Order Information')->schema([
                Forms\Components\TextInput::make('order_number')->disabled(),
                Forms\Components\TextInput::make('tracking_number')->disabled(),
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

            Forms\Components\Section::make('Customer Details')->schema([
                Forms\Components\TextInput::make('customer_name')->disabled(),
                Forms\Components\TextInput::make('customer_email')->disabled(),
                Forms\Components\TextInput::make('customer_phone')->disabled(),
                Forms\Components\TextInput::make('total_amount')
                    ->prefix('RM')
                    ->disabled(),
            ])->columns(['default' => 1, 'sm' => 2]),

            Forms\Components\Section::make('Order Items')->schema([
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
                        $rows = $items->map(fn ($item) =>
                            "<tr class='border-b dark:border-gray-700'>
                                <td class='py-2 pr-4 text-sm'>" . e($item->product_name) . "</td>
                                <td class='py-2 pr-4 text-sm text-center'>{$item->quantity}</td>
                                <td class='py-2 pr-4 text-sm text-right'>RM " . number_format($item->unit_price, 2) . "</td>
                                <td class='py-2 text-sm text-right font-semibold'>RM " . number_format($item->subtotal, 2) . "</td>
                            </tr>"
                        )->implode('');
                        return new \Illuminate\Support\HtmlString("
                            <table class='w-full'>
                                <thead><tr class='text-xs text-gray-500 uppercase border-b dark:border-gray-700'>
                                    <th class='pb-2 text-left'>Product</th>
                                    <th class='pb-2 text-center'>Qty</th>
                                    <th class='pb-2 text-right'>Unit Price</th>
                                    <th class='pb-2 text-right'>Subtotal</th>
                                </tr></thead>
                                <tbody>{$rows}</tbody>
                            </table>
                        ");
                    }),
            ])->visibleOn('edit'),

            Forms\Components\Section::make('Order Notes')->schema([
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
                TextColumn::make('tracking_number')
                    ->label('Tracking')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
