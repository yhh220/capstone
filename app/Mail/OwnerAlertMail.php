<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * A lightweight internal alert to the shop owner (new booking / new enquiry).
 * Kept generic so one queued mailable + one view covers every alert type.
 */
class OwnerAlertMail extends Mailable implements ShouldQueue
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
