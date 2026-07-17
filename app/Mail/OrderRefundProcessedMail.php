<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Sent when an admin marks a cancelled order's refund as actually sent (markRefunded).
// Deliberately separate from OrderCancelledMail — that one says a refund was recorded
// for processing, this one says the money has actually moved. Always English.
class OrderRefundProcessedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Refund Sent – '.$this->order->order_number.' | Win Win Car Audio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order-refund-processed',
        );
    }
}
