<?php

namespace App\Filament\Resources\Logs;

use App\Models\AppLog;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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
            ->whereNull('resolved_at')
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
            ->description(fn (): ?\Illuminate\Contracts\Support\Htmlable => request()->query('trace_id')
                ? new \Illuminate\Support\HtmlString(
                    '<span class="text-sm">Showing only the log lines from one request (trace <code class="font-mono">'
                    . e(request()->query('trace_id'))
                    . '</code>). <a href="' . static::getUrl('index') . '" class="underline font-semibold">Clear</a></span>'
                )
                : null)
            // "View trace" links here with ?trace_id=... . Table filters are a
            // Livewire form whose state Filament does NOT hydrate from the URL
            // query string on a fresh page load, so passing tableFilters[...] in
            // the link (the first thing tried) silently did nothing — the table
            // still showed every row. Reading the query string straight into the
            // base query sidesteps that entirely and always works.
            ->modifyQueryUsing(fn ($query) => $query->when(
                request()->query('trace_id'),
                fn ($q, $traceId) => $q->where('trace_id', $traceId),
            ))
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
                IconColumn::make('resolved_at')
                    ->label('Fixed')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (AppLog $record): string => $record->resolved_at
                        ? 'Marked fixed ' . $record->resolved_at->diffForHumans()
                        : 'Not marked fixed'),
            ])
            ->filters([
                SelectFilter::make('level_name')->label('Level')->multiple()->options([
                    'debug' => 'Debug', 'info' => 'Info', 'notice' => 'Notice', 'warning' => 'Warning',
                    'error' => 'Error', 'critical' => 'Critical', 'alert' => 'Alert', 'emergency' => 'Emergency',
                ]),
                Filter::make('errors_only')->label('Errors & above')->toggle()
                    ->query(fn ($query) => $query->whereIn('level_name', ['error', 'critical', 'alert', 'emergency'])),
                // Old, already-fixed errors stay in the table for the audit trail —
                // this is what hides them from view (and the nav badge) without
                // deleting the history.
                Filter::make('hide_fixed')->label('Hide fixed')->toggle()->default()
                    ->query(fn ($query) => $query->whereNull('resolved_at')),
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
                    ->url(fn (AppLog $record): string => static::getUrl('index', ['trace_id' => $record->trace_id])),
                Action::make('checkFixed')->label('Check for recurrence')->icon(Heroicon::OutlinedMagnifyingGlass)->color('info')
                    ->tooltip("Checks the log history only — it doesn't re-run anything. If the same error hasn't appeared again, it marks this resolved as a best guess; it can't prove the underlying code is actually fixed.")
                    ->visible(fn (AppLog $record): bool => $record->resolved_at === null)
                    ->action(function (AppLog $record): void {
                        ['state' => $state, 'last_seen' => $lastSeen] = $record->recurrenceState();

                        if ($state === 'active') {
                            Notification::make()
                                ->title('Still happening')
                                ->body('This error last occurred ' . $lastSeen->diffForHumans() . ' — too recent to call fixed. Leaving it open.')
                                ->warning()
                                ->send();
                            return;
                        }

                        if ($state === 'recurred') {
                            Notification::make()
                                ->title('Still recurring')
                                ->body('This error happened again after this entry (last seen ' . $lastSeen->diffForHumans() . ') — leaving it open.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Quiet — nothing newer than this entry's burst. Resolve the whole group.
                        $resolved = $record->resolveSiblings();

                        Notification::make()
                            ->title('No recurrence found')
                            ->body('Last seen ' . $lastSeen->diffForHumans() . ", so {$resolved} log " . str('entry')->plural($resolved) . ' marked resolved. This only reflects log history — it has not re-tested the underlying code.')
                            ->success()
                            ->send();
                    }),
                Action::make('markFixed')->label('Mark fixed')->icon(Heroicon::OutlinedCheckCircle)->color('success')
                    ->visible(fn (AppLog $record): bool => $record->resolved_at === null)
                    ->action(fn (AppLog $record) => $record->update(['resolved_at' => now()])),
                Action::make('reopen')->label('Reopen')->icon(Heroicon::OutlinedArrowUturnLeft)->color('gray')
                    ->visible(fn (AppLog $record): bool => $record->resolved_at !== null)
                    ->action(fn (AppLog $record) => $record->update(['resolved_at' => null])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markFixedBulk')->label('Mark fixed')->icon(Heroicon::OutlinedCheckCircle)->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (AppLog $log) => $log->resolved_at ?? $log->update(['resolved_at' => now()]));
                            Notification::make()->title('Marked ' . $records->count() . ' log entr' . ($records->count() === 1 ? 'y' : 'ies') . ' fixed')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogs::route('/'),
        ];
    }
}
