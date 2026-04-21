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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('key')
                ->required()
                ->disabled()
                ->label('Setting Key')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'ONLINE_SHOPPING_ENABLED' => '🛒 Online Shopping Mode',
                    'BUSINESS_HOURS_START'    => '🕒 Business Start Time',
                    'BUSINESS_HOURS_END'      => '🕓 Business End Time',
                    default                   => $state,
                }),
            Forms\Components\TextInput::make('value')
                ->required()
                ->label('Setting Value')
                ->placeholder(fn (Setting $record): string => match ($record->key) {
                    'BUSINESS_HOURS_START', 'BUSINESS_HOURS_END' => 'e.g. 09:00',
                    'ONLINE_SHOPPING_ENABLED' => 'true or false',
                    default => '',
                })
                ->helperText(fn (Setting $record): ?string => match ($record->key) {
                    'ONLINE_SHOPPING_ENABLED' => 'Set to "true" to enable the cart and checkout features, or "false" to hide them.',
                    'BUSINESS_HOURS_START'    => 'The earliest time a customer can book a service (24h format, e.g., 09:00).',
                    'BUSINESS_HOURS_END'      => 'The latest time your shop accepts appointments (24h format, e.g., 18:00).',
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
                        'ONLINE_SHOPPING_ENABLED' => '🛒 Online Shopping Mode',
                        'BUSINESS_HOURS_START'    => '🕒 Business Start Time',
                        'BUSINESS_HOURS_END'      => '🕓 Business End Time',
                        default                   => $state,
                    })
                    ->description(fn (Setting $record): string => match ($record->key) {
                        'ONLINE_SHOPPING_ENABLED' => 'Toggle shopping cart & checkout availability.',
                        'BUSINESS_HOURS_START'    => 'Opening time for booking services.',
                        'BUSINESS_HOURS_END'      => 'Closing time for booking services.',
                        default                   => 'System configuration setting.',
                    }),
                TextColumn::make('value')
                    ->searchable()
                    ->badge()
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
