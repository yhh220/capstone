<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table->paginated([10, 25, 50])
            ->defaultSort('preferred_date', 'asc')
            ->columns([
                TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_phone')
                    ->searchable(),
                TextColumn::make('vehicle_model')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('service.name')
                    ->label('About')
                    ->placeholder('General visit')
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->formatStateUsing(fn ($state) => $state ?: 'General visit')
                    ->sortable(),
                TextColumn::make('preferred_date')
                    ->date('D, d M Y')
                    ->sortable(),
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                        default     => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('reminder_sent_at')
                    ->label('Reminder')
                    ->badge()
                    ->state(fn ($record) => $record->reminder_sent_at ? 'Sent' : 'Not sent')
                    ->color(fn ($record) => $record->reminder_sent_at ? 'success' : 'gray')
                    ->tooltip(fn ($record) => $record->reminder_sent_at?->format('d M Y, H:i'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('service')
                    ->relationship('service', 'name')
                    ->multiple()
                    ->preload(),
                \Filament\Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming only')
                    ->toggle()
                    ->query(fn ($query) => $query->whereDate('preferred_date', '>=', today())),
            ])
            ->persistFiltersInSession()
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\Action::make('sendReminder')
                    ->label('Send reminder')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedBell)
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'confirmed'], true) && filled($record->customer_email))
                    ->requiresConfirmation()
                    ->modalHeading('Send a reminder email now?')
                    ->action(function ($record): void {
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->customer_email)
                                ->send(new \App\Mail\BookingReminderMail($record->load('service')));
                            $record->forceFill(['reminder_sent_at' => now()])->save();
                            \Filament\Notifications\Notification::make()->title('Reminder sent')->success()->send();
                        } catch (\Throwable $e) {
                            logger()->error('Manual booking reminder failed: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()->title('Reminder failed to send')->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('confirm')
                        ->label('Confirm selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records
                            ->each(fn ($b) => $b->status === 'pending' && $b->update(['status' => 'confirmed'])))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
