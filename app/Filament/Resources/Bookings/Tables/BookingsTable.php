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
                    ->toggleable()
                    ->tooltip('Vehicle model specified by the customer'),
                TextColumn::make('service.name')
                    ->label('About')
                    ->placeholder('General visit')
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'warning')
                    ->formatStateUsing(fn ($state) => $state ?: 'General visit')
                    ->tooltip(fn ($record) => $record->service ? "Service: {$record->service->name}" : "General visit (unspecified service)")
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
                    ->tooltip(fn (string $state): string => match ($state) {
                        'confirmed' => 'Booking has been confirmed',
                        'cancelled' => 'Booking was cancelled',
                        'completed' => 'Service has been completed',
                        default     => 'Awaiting action/confirmation',
                    })
                    ->sortable(),
                TextColumn::make('reminder_sent_at')
                    ->label('Reminder')
                    ->badge()
                    ->state(fn ($record) => $record->reminder_sent_at ? 'Sent' : 'Not sent')
                    ->color(fn ($record) => $record->reminder_sent_at ? 'success' : 'gray')
                    ->tooltip(fn ($record) => $record->reminder_sent_at ? 'Sent at: ' . $record->reminder_sent_at->format('d M Y, H:i') : 'No reminder sent yet')
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
                EditAction::make()
                    ->tooltip('Edit booking details'),
                \Filament\Actions\Action::make('confirmBooking')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Confirm this booking and email the customer')
                    ->authorize(fn () => auth()->user()?->isAdmin())
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm this booking?')
                    ->modalDescription('The customer will receive a confirmation email.')
                    ->action(function ($record): void {
                        // Atomic conditional flip — the customer may have cancelled
                        // between the page render and this click; blindly updating
                        // would resurrect a cancelled booking (whose slot could
                        // already be re-booked) and email a confirmation for it.
                        // Mirrors OrderResource markPaid's claim pattern.
                        $claimed = \App\Models\Booking::whereKey($record->getKey())
                            ->where('status', 'pending')
                            ->update(['status' => 'confirmed']) === 1;

                        if (! $claimed) {
                            \Filament\Notifications\Notification::make()
                                ->title('Booking was already confirmed or cancelled')
                                ->warning()
                                ->send();
                            return;
                        }

                        try {
                            if ($record->customer_email) {
                                \Illuminate\Support\Facades\Mail::to($record->customer_email)
                                    ->send(new \App\Mail\BookingConfirmedMail($record->fresh('service')));
                            }
                            \Filament\Notifications\Notification::make()->title('Booking confirmed')->success()->send();
                        } catch (\Throwable $e) {
                            logger()->error('BookingConfirmedMail failed: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()->title('Confirmed, but the email failed to send')->warning()->send();
                        }
                    }),
                \Filament\Actions\Action::make('sendReminder')
                    ->label('Send reminder')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedBell)
                    ->color('warning')
                    ->tooltip('Send a reminder email to customer now')
                    ->authorize(fn () => auth()->user()?->isAdmin())
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
                        ->tooltip('Confirm all selected pending bookings and email each customer')
                        ->authorize(fn () => auth()->user()?->isAdmin())
                        ->requiresConfirmation()
                        ->modalDescription('Each confirmed customer will receive a confirmation email.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(function ($b) {
                                // Same atomic claim as the single Confirm action —
                                // only a still-pending row flips, so a booking
                                // cancelled mid-selection is never resurrected.
                                $claimed = \App\Models\Booking::whereKey($b->getKey())
                                    ->where('status', 'pending')
                                    ->update(['status' => 'confirmed']) === 1;

                                if (! $claimed) {
                                    return;
                                }

                                try {
                                    if ($b->customer_email) {
                                        \Illuminate\Support\Facades\Mail::to($b->customer_email)
                                            ->send(new \App\Mail\BookingConfirmedMail($b->fresh('service')));
                                    }
                                } catch (\Throwable $e) {
                                    logger()->error('Bulk BookingConfirmedMail failed: ' . $e->getMessage());
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
