<?php

namespace App\Filament\Resources\Logs;

use App\Models\AppLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogResource extends Resource
{
    protected static ?string $model = AppLog::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static \UnitEnum|string|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 93;
    protected static ?string $navigationLabel = 'Logs';
    protected static ?string $modelLabel = 'log entry';

    public static function canViewAny(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) AppLog::whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])
            ->where('logged_at', '>=', now()->subDay())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('logged_at')->label('Time')->dateTime('d M H:i:s')->sortable(),
                TextColumn::make('level_name')->label('Level')->badge()
                    ->color(fn (AppLog $record): string => $record->levelColor())
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),
                TextColumn::make('message')->wrap()->limit(140)->searchable(),
                TextColumn::make('trace_id')->label('Trace')->copyable()->limit(8)->placeholder('—')->toggleable(),
                TextColumn::make('user_id')->label('User')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('path')->label('Path')->limit(30)->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level_name')->label('Level')->multiple()->options([
                    'debug' => 'Debug', 'info' => 'Info', 'notice' => 'Notice', 'warning' => 'Warning',
                    'error' => 'Error', 'critical' => 'Critical', 'alert' => 'Alert', 'emergency' => 'Emergency',
                ]),
                Filter::make('errors_only')->label('Errors & above')->toggle()
                    ->query(fn ($query) => $query->whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])),
                Filter::make('trace')
                    ->schema([Forms\Components\TextInput::make('trace_id')->label('Trace ID')])
                    ->query(fn ($query, array $data) => $query->when($data['trace_id'] ?? null, fn ($q, $t) => $q->where('trace_id', $t)))
                    ->indicateUsing(fn (array $data): ?string => ($data['trace_id'] ?? null) ? 'Trace: ' . $data['trace_id'] : null),
                Filter::make('logged_at')
                    ->schema([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('logged_at', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('logged_at', '<=', $d))),
            ])
            ->recordActions([
                Action::make('details')->label('Details')->icon(Heroicon::OutlinedEye)
                    ->modalHeading(fn (AppLog $record): string => strtoupper($record->level_name) . ' · ' . $record->logged_at->format('d M Y H:i:s'))
                    ->modalContent(fn (AppLog $record) => view('filament.logs.detail', ['log' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Action::make('trace')->label('View trace')->icon(Heroicon::OutlinedArrowsRightLeft)
                    ->visible(fn (AppLog $record): bool => filled($record->trace_id))
                    ->url(fn (AppLog $record): string => static::getUrl('index', [
                        'tableFilters' => ['trace' => ['trace_id' => $record->trace_id]],
                    ])),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogs::route('/'),
        ];
    }
}
