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

    /**
     * The To address for owner alerts: a "+alerts" alias of the shop's own
     * Gmail (overridable via STORE_ALERT_EMAIL). The alias delivers to the
     * same mailbox, but gives Gmail filters something to match — self-sent
     * mail never gets the INBOX label, so a filter on this alias applies a
     * label that serves as the owner's notification tray. Null when no store
     * email is configured (callers skip the alert).
     */
    public static function recipient(): ?string
    {
        if ($override = config('services.store.alert_email')) {
            return $override;
        }

        $email = (string) config('services.store.email');

        return $email !== '' ? str_replace('@', '+alerts@', $email) : null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->heading . ' | ' . config('services.store.seo_name', 'Win Win Car Audio'),
            cc: $this->adminCc(),
        );
    }

    /**
     * CC every admin-role user so alerts land in the staff's own inboxes.
     * The To address is the shop's own Gmail — the same account the mail is
     * sent from — and Gmail never delivers self-sent mail to its inbox (it
     * only appears under Sent), so without the CC nobody is actually
     * notified. Excludes the To address in case an admin logs in with it.
     *
     * @return list<string>
     */
    private function adminCc(): array
    {
        return \App\Models\User::query()
            ->where('role', 'admin')
            ->where('email', '!=', (string) config('services.store.email'))
            ->pluck('email')
            ->all();
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.owner-alert',
        );
    }
}
