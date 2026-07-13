<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPickupRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pickup time updated – '.$this->order->order_number.' | Win Win Car Audio');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.order-pickup-rescheduled');
    }
}
