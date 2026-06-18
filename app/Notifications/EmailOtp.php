<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Emails a 6-digit verification code for either registration or password reset.
 * Sent on-demand (Notification::route) because during registration the user
 * record does not exist yet.
 */
class EmailOtp extends Notification
{
    use Queueable;

    public function __construct(
        public string $code,
        public string $purpose,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isReset = $this->purpose === 'pwreset';
        $store   = config('services.store.seo_name', 'Win Win Car Audio');

        $subject = $isReset
            ? __('Your password reset code')
            : __('Your verification code');

        return (new MailMessage)
            ->subject($subject . ' — ' . $store)
            ->view('mail.email-otp', [
                'code'    => $this->code,
                'isReset' => $isReset,
                'minutes' => 10,
            ]);
    }
}
