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
                ->label('Setting Key'),
            Forms\Components\TextInput::make('value')
                ->required()
                ->label('Value')
                ->helperText('For boolean settings, use "true" or "false".'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Setting')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
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
