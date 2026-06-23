<?php

namespace App\Providers;

use App\Mail\Transport\GmailApiTransport;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Request-scoped breadcrumb trail (attached to error logs by the
        // ObservabilityProcessor).
        $this->app->singleton(\App\Support\Breadcrumbs::class);
    }

    public function boot(): void
    {
        // Single source of truth for password strength, applied everywhere via
        // Password::defaults() (registration, password reset, profile change):
        // 8+ chars with mixed case, a number and a symbol, and rejected if found
        // in a known breach (HaveIBeenPwned, k-anonymity). The breach check is
        // skipped under testing to avoid external HTTP calls + flakiness.
        Password::defaults(function () {
            $rule = Password::min(8)->mixedCase()->numbers()->symbols();

            return app()->environment('testing') ? $rule : $rule->uncompromised();
        });

        // Register the Microsoft Socialite driver (Google ships with Socialite).
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('microsoft', \SocialiteProviders\Microsoft\Provider::class);
        });

        // Gmail-over-HTTPS mail transport (see GmailApiTransport docblock) —
        // selected via MAIL_MAILER=gmail_api in config/mail.php.
        Mail::extend('gmail_api', function () {
            return new GmailApiTransport(
                config('services.google.client_id'),
                config('services.google.client_secret'),
                config('services.google.gmail_send_refresh_token'),
            );
        });
    }
}
