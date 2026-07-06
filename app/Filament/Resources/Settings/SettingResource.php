<?php

namespace App\Filament\Resources\Settings;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static \UnitEnum|string|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 99;
    protected static ?string $navigationLabel = 'Settings';

    public static function canViewAny(): bool
    {
        return \Filament\Facades\Filament::auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('key')
                ->required()
                ->disabled()
                ->label('Setting Key')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'ONLINE_SHOPPING_ENABLED' => 'Online Shopping Mode',
                    'BUSINESS_HOURS_START'    => 'Business Start Time',
                    'BUSINESS_HOURS_END'      => 'Business End Time',
                    'BUSINESS_CLOSED_WEEKDAYS'=> 'Closed Weekdays',
                    'BOOKING_SLOT_MINUTES'    => 'Appointment Slot Length',
                    'BACKORDER_DAYS'          => 'Backorder Lead Time (days)',
                    'SHIPPING_FLAT_RATE'      => 'Shipping Flat Rate (RM)',
                    'SHIPPING_FREE_THRESHOLD' => 'Free Shipping Threshold (RM)',
                    'CANCELLATION_FULL_REFUND_HOURS' => 'Full Refund Window (hours)',
                    'CANCELLATION_FEE_PERCENT'       => 'Cancellation Fee (%)',
                    'SITE_ANNOUNCEMENT_ENABLED' => 'Site Announcement Bar',
                    'SITE_ANNOUNCEMENT_TEXT'    => 'Site Announcement Text',
                    default                   => $state,
                }),
            Forms\Components\TextInput::make('value')
                ->required()
                ->label('Setting Value')
                // Every numeric setting is already independently clamped to a sane
                // range wherever it's consumed (RefundCalculator, BookingService,
                // ShippingCalculator), so a bad value here can't crash or miscalculate
                // anything downstream — but it should still be rejected here with a
                // clear error instead of silently saving "abc" into a number field.
                ->rules(fn (Setting $record): array => match ($record->key) {
                    'ONLINE_SHOPPING_ENABLED' => ['in:true,false'],
                    'SITE_ANNOUNCEMENT_ENABLED' => ['in:true,false'],
                    'SITE_ANNOUNCEMENT_TEXT'    => ['nullable', 'string', 'max:300'],
                    'BUSINESS_HOURS_START', 'BUSINESS_HOURS_END' => ['date_format:H:i'],
                    'BUSINESS_CLOSED_WEEKDAYS' => ['regex:/^\s*[0-6](\s*,\s*[0-6])*\s*$/'],
                    'BOOKING_SLOT_MINUTES' => ['integer', 'min:15'],
                    'BACKORDER_DAYS' => ['integer', 'min:0'],
                    'SHIPPING_FLAT_RATE', 'SHIPPING_FREE_THRESHOLD' => ['numeric', 'min:0'],
                    'CANCELLATION_FULL_REFUND_HOURS' => ['integer', 'min:0'],
                    'CANCELLATION_FEE_PERCENT' => ['numeric', 'min:0', 'max:100'],
                    default => [],
                })
                ->placeholder(fn (Setting $record): string => match ($record->key) {
                    'SITE_ANNOUNCEMENT_ENABLED' => 'true or false',
                    'SITE_ANNOUNCEMENT_TEXT'    => 'e.g. Online shopping is under maintenance…',
                    'BUSINESS_HOURS_START', 'BUSINESS_HOURS_END' => 'e.g. 09:00',
                    'BUSINESS_CLOSED_WEEKDAYS' => 'e.g. 5 for Friday, or 0,5 for Sunday and Friday',
                    'ONLINE_SHOPPING_ENABLED' => 'true or false',
                    'BOOKING_SLOT_MINUTES' => 'e.g. 30',
                    'BACKORDER_DAYS' => 'e.g. 7',
                    'SHIPPING_FLAT_RATE' => 'e.g. 10',
                    'SHIPPING_FREE_THRESHOLD' => 'e.g. 300 (0 to disable free shipping)',
                    'CANCELLATION_FULL_REFUND_HOURS' => 'e.g. 24',
                    'CANCELLATION_FEE_PERCENT' => 'e.g. 10',
                    default => '',
                })
                ->helperText(fn (Setting $record): ?string => match ($record->key) {
                    'ONLINE_SHOPPING_ENABLED' => 'Set to "true" to enable cart & checkout, or "false" for showroom mode. Turning it OFF cancels & restocks all unpaid orders — remember to turn on the Site Announcement Bar so customers know shopping is paused.',
                    'SITE_ANNOUNCEMENT_ENABLED' => 'Set to "true" to show a banner at the top of every page (e.g. to tell customers online shopping is temporarily under maintenance). "false" hides it.',
                    'SITE_ANNOUNCEMENT_TEXT'    => 'The message shown in the announcement banner when it is turned on. Keep it short (max 300 characters).',
                    'BUSINESS_HOURS_START'    => 'The earliest time a customer can book a service (24h format, e.g., 09:00).',
                    'BUSINESS_HOURS_END'      => 'The latest time your shop accepts appointments (24h format, e.g., 18:00).',
                    'BUSINESS_CLOSED_WEEKDAYS'=> 'Comma-separated weekday numbers: 0=Sunday, 1=Monday, ... 5=Friday, 6=Saturday.',
                    'BOOKING_SLOT_MINUTES'    => 'How long each appointment slot is, in minutes (e.g. 30). Controls the times customers can pick.',
                    'BACKORDER_DAYS'          => 'How many days to tell customers an out-of-stock (backordered) item takes to arrive.',
                    'SHIPPING_FLAT_RATE'      => 'Flat delivery fee (RM) charged when the subtotal is below the free-shipping threshold. Set to 0 for always-free shipping.',
                    'SHIPPING_FREE_THRESHOLD' => 'Spend this much (RM) or more and shipping is free. Set to 0 to always charge the flat rate.',
                    'CANCELLATION_FULL_REFUND_HOURS' => 'Hours after payment during which a customer-cancelled order gets a 100% refund. After this window (but before shipping), the cancellation fee below applies instead.',
                    'CANCELLATION_FEE_PERCENT'       => 'Processing fee (%) deducted from the refund when a paid order is cancelled after the full-refund window has passed, but before it ships.',
                    default                   => 'Enter the value for this setting.',
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Setting Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ONLINE_SHOPPING_ENABLED' => 'Online Shopping Mode',
                        'BUSINESS_HOURS_START'    => 'Business Start Time',
                        'BUSINESS_HOURS_END'      => 'Business End Time',
                        'BUSINESS_CLOSED_WEEKDAYS'=> 'Closed Weekdays',
                        'BOOKING_SLOT_MINUTES'    => 'Appointment Slot Length',
                        'BACKORDER_DAYS'          => 'Backorder Lead Time (days)',
                        'SHIPPING_FLAT_RATE'      => 'Shipping Flat Rate (RM)',
                        'SHIPPING_FREE_THRESHOLD' => 'Free Shipping Threshold (RM)',
                        'CANCELLATION_FULL_REFUND_HOURS' => 'Full Refund Window (hours)',
                        'CANCELLATION_FEE_PERCENT'       => 'Cancellation Fee (%)',
                        'SITE_ANNOUNCEMENT_ENABLED' => 'Site Announcement Bar',
                        'SITE_ANNOUNCEMENT_TEXT'    => 'Site Announcement Text',
                        default                   => $state,
                    })
                    ->description(fn (Setting $record): string => match ($record->key) {
                        'ONLINE_SHOPPING_ENABLED' => 'Toggle shopping cart & checkout availability.',
                        'BUSINESS_HOURS_START'    => 'Opening time for booking services.',
                        'BUSINESS_HOURS_END'      => 'Closing time for booking services.',
                        'BUSINESS_CLOSED_WEEKDAYS'=> 'Weekdays where bookings are unavailable.',
                        'BOOKING_SLOT_MINUTES'    => 'Length of each appointment slot, in minutes.',
                        'BACKORDER_DAYS'          => 'Days quoted for out-of-stock (backordered) items to arrive.',
                        'SHIPPING_FLAT_RATE'      => 'Flat delivery fee charged below the free-shipping threshold.',
                        'SHIPPING_FREE_THRESHOLD' => 'Spend this much (RM) or more and shipping is free.',
                        'CANCELLATION_FULL_REFUND_HOURS' => 'Hours after payment a cancelled order still gets a 100% refund.',
                        'CANCELLATION_FEE_PERCENT'       => 'Fee deducted from the refund after the full-refund window passes.',
                        'SITE_ANNOUNCEMENT_ENABLED' => 'Show/hide the site-wide announcement banner.',
                        'SITE_ANNOUNCEMENT_TEXT'    => 'The message shown in the announcement banner.',
                        default                   => 'System configuration setting.',
                    }),
                TextColumn::make('value')
                    ->searchable()
                    ->badge()
                    // The announcement-text setting is a long sentence; without a
                    // limit its non-wrapping badge stretches the whole column and
                    // pushes the row actions off-screen. Truncate long values (full
                    // text on hover); short values (true/false, numbers, times) are
                    // unaffected.
                    ->limit(45)
                    ->tooltip(fn (string $state): ?string => mb_strlen($state) > 45 ? $state : null)
                    ->color(fn(string $state): string => match ($state) {
                        'true'  => 'success',
                        'false' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('toggle')
                    ->label('Toggle')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('warning')
                    ->visible(fn(Setting $record) => in_array($record->value, ['true', 'false']))
                    ->requiresConfirmation()
                    ->modalHeading(fn(Setting $record) => "Toggle {$record->key}?")
                    ->modalDescription(fn(Setting $record) => "Current: {$record->value} → " . ($record->value === 'true' ? 'false' : 'true'))
                    ->action(function (Setting $record) {
                        $newValue = $record->value === 'true' ? 'false' : 'true';
                        Setting::setValue($record->key, $newValue);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit'  => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
