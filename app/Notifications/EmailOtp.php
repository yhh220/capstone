<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Emails a 6-digit verification code for registration, password reset, setting
 * a password, enabling login 2FA, or a login itself. Sent on-demand
 * (Notification::route) because during registration the user record does not
 * exist yet.
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
        $store = config('services.store.seo_name', 'Win Win Car Audio');

        $subject = match ($this->purpose) {
            'pwreset'   => __('Your password reset code'),
            'login2fa'  => __('Your login verification code'),
            'enable2fa' => __('Your security code'),
            'setpw'     => __('Your password setup code'),
            default     => __('Your verification code'),
        };

        return (new MailMessage)
            ->subject($subject . ' — ' . $store)
            ->view('mail.email-otp', [
                'code'    => $this->code,
                'purpose' => $this->purpose,
                'minutes' => 10,
            ]);
    }
}
