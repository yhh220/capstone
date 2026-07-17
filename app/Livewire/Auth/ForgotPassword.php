<?php

namespace App\Livewire\Auth;

use App\Exceptions\OtpSendFailedException;
use App\Livewire\Concerns\SetsSeo;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class ForgotPassword extends Component
{
    use SetsSeo, UsesSpamProtection;

    public HoneypotData $honeypotData;

    /** 1 = request code · 2 = enter code + new password · 3 = done */
    public int $step = 1;

    public string $email = '';

    public string $otpCode = '';

    public bool $showPassword = false;

    // password/password_confirmation are intentionally NOT public properties —
    // Livewire serializes every public property into wire:snapshot (visible in
    // page source / the Network tab). They're bound via Alpine-only state and
    // passed into resetPassword() as #[\SensitiveParameter] method arguments.

    public function mount(): void
    {
        $this->honeypotData = new HoneypotData;

        if (Auth::check()) {
            $this->redirect(route('home'), navigate: false);

            return;
        }

        $this->setSeo(
            title: __('Forgot Password'),
            description: 'Reset your Win Win Car Audio account password with a one-time email code.',
        );
    }

    /**
     * Step 1 → 2. A code is only really sent to a registered email, but the
     * response is identical either way so the form can't be used to discover
     * which emails have accounts (no enumeration).
     */
    public function sendCode(): void
    {
        // Livewire keeps the error bag across requests and addError() appends,
        // while @error shows first() — reset so retries report fresh messages.
        $this->resetErrorBag();

        // Honeypot (hidden field + submission time gate) — silently blocks bots.
        $this->protectAgainstSpam();

        $this->validate(['email' => ['required', 'email']]);

        // Per-IP throttle so the form can't be looped over many emails to blast
        // OTP mail / burn the SMTP quota. Applied to every attempt (whether or
        // not the email exists) so it never reveals which emails have accounts.
        $ip = request()->ip();
        $key = 'pwreset:'.$ip;
        $dailyKey = 'pwreset-daily:'.$ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', __('Too many submissions. Please wait :seconds seconds before trying again.', ['seconds' => $seconds]));

            return;
        }
        if (RateLimiter::tooManyAttempts($dailyKey, 15)) {
            $this->addError('email', __('You have reached today’s message limit. Please WhatsApp us directly instead.'));

            return;
        }

        RateLimiter::hit($key, 600);        // 10-minute burst window
        RateLimiter::hit($dailyKey, 86400); // 24-hour daily window

        $otp = app(EmailOtpService::class);

        if (
            $otp->resendAvailableIn(EmailOtpService::PURPOSE_RESET, $this->email) === 0
            && User::where('email', $this->email)->exists()
        ) {
            try {
                $otp->send(EmailOtpService::PURPOSE_RESET, $this->email);
            } catch (OtpSendFailedException $e) {
                $this->addError('email', $e->getMessage());

                return;
            }
        }

        $this->step = 2;
        $this->otpCode = '';
        session()->flash('reset_sent', __('If an account exists for that email, a 6-digit code has been sent.'));
    }

    /**
     * Resend the reset code (throttled to once per 60s).
     */
    public function resendCode(): void
    {
        $this->resetErrorBag(); // same stale-first()-message trap as sendCode()

        // Resends count toward both the per-IP burst cap and the daily cap.
        $ip = request()->ip();
        $key = 'pwreset:'.$ip;
        $dailyKey = 'pwreset-daily:'.$ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('otpCode', __('Too many submissions. Please wait :seconds seconds before trying again.', ['seconds' => $seconds]));

            return;
        }
        if (RateLimiter::tooManyAttempts($dailyKey, 15)) {
            $this->addError('otpCode', __('You have reached today’s message limit. Please WhatsApp us directly instead.'));

            return;
        }

        $otp = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_RESET, $this->email);

        if ($wait > 0) {
            $this->addError('otpCode', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));

            return;
        }

        RateLimiter::hit($key, 600);
        RateLimiter::hit($dailyKey, 86400);

        if (User::where('email', $this->email)->exists()) {
            try {
                $otp->send(EmailOtpService::PURPOSE_RESET, $this->email);
            } catch (OtpSendFailedException $e) {
                $this->addError('otpCode', $e->getMessage());

                return;
            }
        }

        session()->flash('reset_sent', __('If an account exists for that email, a 6-digit code has been sent.'));
    }

    /**
     * Step 2 → 3. Verify the code and set the new password.
     */
    public function resetPassword(
        #[\SensitiveParameter] string $password = '',
        #[\SensitiveParameter] string $passwordConfirmation = '',
    ): void {
        $this->resetErrorBag(); // same stale-first()-message trap as sendCode()

        $v = Validator::make(
            [
                'otpCode' => $this->otpCode,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'otpCode' => ['required', 'digits:6'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'password_confirmation' => ['required'],
            ],
            [
                'password.min' => __('Password must be at least 8 characters.'),
            ],
        );

        if ($v->fails()) {
            foreach ($v->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            return;
        }

        $otp = app(EmailOtpService::class);

        if (! $otp->verify(EmailOtpService::PURPOSE_RESET, $this->email, $this->otpCode)) {
            $this->addError('otpCode', __('Invalid or expired code. Please try again.'));

            return;
        }

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('otpCode', __('Invalid or expired code. Please try again.'));

            return;
        }

        // The 'hashed' cast hashes the new password on save.
        $user->forceFill(['password' => $password])->save();

        $this->reset('otpCode');
        $this->step = 3;
    }

    public function backToEmail(): void
    {
        $this->step = 1;
        $this->otpCode = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.app');
    }
}
