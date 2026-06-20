<?php

namespace App\Livewire\Concerns;

use App\Mail\OwnerAlertMail;
use Illuminate\Support\Facades\Mail;

trait NotifiesOwner
{
    /**
     * Email the shop owner about a new submission.
     *
     * @param  array<string, string|null>  $rows
     */
    protected function notifyOwner(string $heading, array $rows, ?string $actionUrl = null, ?string $actionLabel = null): void
    {
        $email = config('services.store.email');

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new OwnerAlertMail($heading, $rows, $actionUrl, $actionLabel));
        } catch (\Throwable $e) {
            logger()->error('Owner alert email failed: ' . $e->getMessage());
        }
    }
}
