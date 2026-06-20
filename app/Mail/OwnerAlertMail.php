<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A lightweight internal alert to the shop owner (new booking / new enquiry).
 * Kept generic so one mailable + one view covers every alert type. Sent
 * synchronously (no ShouldQueue) so it goes out without a queue worker.
 */
class OwnerAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string|null>  $rows  Label => value pairs shown in the email.
     */
    public function __construct(
        public string $heading,
        public array $rows,
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
    ) {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->heading . ' | ' . config('services.store.seo_name', 'Win Win Car Audio'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.owner-alert',
        );
    }
}
