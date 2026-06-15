<?php

namespace App\Livewire\Concerns;

use App\Mail\OwnerAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

trait NotifiesOwner
{
    /**
     * Email the shop owner about a new submission — but never more than a fixed
     * number of alert emails per hour, no matter how many submissions arrive.
     * A spam burst therefore cannot flood the owner's inbox; the overflow is
     * still fully visible in the admin panel (with its unread badge).
     *
     * @param  array<string, string|null>  $rows
     */
    protected function notifyOwner(string $heading, array $rows, ?string $actionUrl = null, ?string $actionLabel = null): void
    {
        $email = config('services.store.email');

        if (! $email) {
            return;
        }

        // Shared key across booking + enquiry alerts: at most 10 owner emails/hour.
        RateLimiter::attempt(
            'owner-alert-email',
            maxAttempts: 10,
            callback: function () use ($email, $heading, $rows, $actionUrl, $actionLabel) {
                Mail::to($email)->queue(new OwnerAlertMail($heading, $rows, $actionUrl, $actionLabel));
            },
            decaySeconds: 3600,
        );
    }
}
