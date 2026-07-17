<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Cancelled – '.$this->booking->reference.' | Win Win Car Audio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking-cancelled',
        );
    }
}
