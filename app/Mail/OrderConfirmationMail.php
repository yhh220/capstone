<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Sent synchronously (no ShouldQueue) so confirmations go out without a worker.
class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed – ' . $this->order->order_number . ' | Win Win Car Audio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order-confirmation',
        );
    }

    /**
     * Attach the invoice as a PDF (same template as the on-site invoice).
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoice', ['order' => $this->order, 'pdf' => true]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'invoice-' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
