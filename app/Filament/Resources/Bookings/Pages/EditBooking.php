<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Re-runs the same slot/closed-day guards CreateBooking enforces — editing
     * previously had none, so an admin could reschedule a booking onto a closed
     * day or directly on top of another booking with no warning.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $startAt = ! empty($data['start_at']) ? Carbon::parse($data['start_at']) : null;

        // Force end_at to be computed from start_at using the system slot length,
        // same as CreateBooking does — prevents an inflated raw end_at from
        // ghost-blocking all subsequent slots on that day.
        if ($startAt) {
            $data['end_at'] = app(BookingService::class)->buildEndAt($startAt);
        }

        $statusBefore = $record->status;
        $startAtBefore = $record->start_at;

        if ($startAt && app(BookingService::class)->isClosedDate($startAt)) {
            Notification::make()
                ->title('The shop is closed on that day')
                ->body('Please pick a different date.')
                ->danger()
                ->send();

            $this->halt();
        }

        // Same phantom-read gap as BookingForm::submit() — lockForUpdate() can't
        // block on a row that doesn't exist yet, so two concurrent reschedules
        // onto the same never-before-booked slot could both pass the check. Keyed
        // identically to BookingForm's lock so a customer booking and an admin
        // rescheduling onto the same slot at the same instant also serialise.
        $lock = $startAt ? Cache::lock('booking-slot:' . $startAt->toDateTimeString(), 10) : null;

        if ($lock && ! $lock->get()) {
            Notification::make()
                ->title('That slot is already booked')
                ->body('Please pick a different start time.')
                ->danger()
                ->send();

            $this->halt();
        }

        try {
            DB::transaction(function () use ($record, $data, $startAt) {
                if ($startAt && ! app(BookingService::class)->isSlotAvailable($startAt, ignoreBookingId: $record->getKey(), lock: true)) {
                    throw new \RuntimeException('This slot is already booked.');
                }

                $record->update($data);
            });
        } catch (\RuntimeException) {
            Notification::make()
                ->title('That slot is already booked')
                ->body('Please pick a different start time.')
                ->danger()
                ->send();

            $this->halt();
        } finally {
            $lock?->release();
        }

        // Rescheduling invalidates a reminder already sent for the old time —
        // clear it so the daily reminder job can send a fresh one for the new date.
        if ($startAt && $startAtBefore && ! $startAtBefore->equalTo($startAt) && $record->reminder_sent_at !== null) {
            $record->forceFill(['reminder_sent_at' => null])->saveQuietly();
        }

        $this->notifyStatusChange($record, $statusBefore);

        return $record;
    }

    /**
     * Editing the status here used to be a silent, email-less duplicate of the
     * dedicated "Confirm" table action and the customer's own cancel flow — the
     * record changed but the customer was never told. Mirror those notifications
     * whenever the status actually changes via this form too.
     */
    private function notifyStatusChange(Booking $booking, string $statusBefore): void
    {
        if ($statusBefore === $booking->status || ! $booking->customer_email) {
            return;
        }

        $mailable = match ($booking->status) {
            'confirmed' => new BookingConfirmedMail($booking->fresh('service')),
            'cancelled' => new BookingCancelledMail($booking->fresh('service')),
            default     => null,
        };

        if (! $mailable) {
            return;
        }

        try {
            Mail::to($booking->customer_email)->send($mailable);
        } catch (\Throwable $e) {
            logger()->error('Booking status-change email failed from admin edit: ' . $e->getMessage());

            Notification::make()
                ->title('Saved, but the notification email failed to send')
                ->warning()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
