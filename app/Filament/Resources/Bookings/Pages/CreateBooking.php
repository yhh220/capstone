<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reference'] ??= Booking::generateReference();

        return $data;
    }

    protected function handleRecordCreation(array $data): Booking
    {
        $startAt = ! empty($data['start_at']) ? Carbon::parse($data['start_at']) : null;

        try {
            $booking = DB::transaction(function () use ($data, $startAt) {
                if ($startAt && ! app(BookingService::class)->isSlotAvailable($startAt, lock: true)) {
                    throw new \RuntimeException('This slot is already booked.');
                }

                return static::getModel()::create($data);
            });
        } catch (\RuntimeException) {
            Notification::make()
                ->title('That slot is already booked')
                ->body('Please pick a different start time.')
                ->danger()
                ->send();

            $this->halt();

            // $this->halt() throws internally — this line is unreachable, but
            // satisfies the method's non-nullable Booking return type.
            return new Booking();
        }

        if ($booking->customer_email) {
            try {
                Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking->fresh('service')));
            } catch (\Throwable $e) {
                logger()->error('Admin-created booking confirmation email failed: ' . $e->getMessage());
            }
        }

        return $booking;
    }
}
