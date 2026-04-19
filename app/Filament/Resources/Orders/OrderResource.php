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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Order Information')->schema([
                Forms\Components\TextInput::make('order_number')->disabled(),
                Forms\Components\TextInput::make('tracking_number')->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'    => '📋 Pending',
                        'processing' => '⏳ Processing',
                        'shipped'    => '📦 Shipped',
                        'delivered'  => '✅ Delivered',
                        'cancelled'  => '❌ Cancelled',
                    ])
                    ->required(),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                    ])
                    ->required(),
            ]),

            Forms\Components\Section::make('Customer Details')->schema([
                Forms\Components\TextInput::make('customer_name')->disabled(),
                Forms\Components\TextInput::make('customer_email')->disabled(),
                Forms\Components\TextInput::make('customer_phone')->disabled(),
                Forms\Components\TextInput::make('total_amount')
                    ->prefix('RM')
                    ->disabled(),
            ]),
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
                    ->money('MYR')
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
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),
            ])
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
