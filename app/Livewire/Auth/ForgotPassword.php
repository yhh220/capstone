<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\SetsSeo;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    use SetsSeo;

    /** 1 = request code · 2 = enter code + new password · 3 = done */
    public int $step = 1;

    public string $email                 = '';
    public string $otpCode               = '';
    public string $password              = '';
    public string $password_confirmation = '';
    public bool   $showPassword          = false;

    public function mount(): void
    {
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
        $this->validate(['email' => ['required', 'email']]);

        $otp = app(EmailOtpService::class);

        if (
            $otp->resendAvailableIn(EmailOtpService::PURPOSE_RESET, $this->email) === 0
            && User::where('email', $this->email)->exists()
        ) {
            $otp->send(EmailOtpService::PURPOSE_RESET, $this->email);
        }

        $this->step    = 2;
        $this->otpCode = '';
        session()->flash('reset_sent', __('If an account exists for that email, a 6-digit code has been sent.'));
    }

    /**
     * Resend the reset code (throttled to once per 60s).
     */
    public function resendCode(): void
    {
        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_RESET, $this->email);

        if ($wait > 0) {
            $this->addError('otpCode', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        if (User::where('email', $this->email)->exists()) {
            $otp->send(EmailOtpService::PURPOSE_RESET, $this->email);
        }

        session()->flash('reset_sent', __('If an account exists for that email, a 6-digit code has been sent.'));
    }

    /**
     * Step 2 → 3. Verify the code and set the new password.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'otpCode'               => ['required', 'digits:6'],
            'password'              => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
            'password_confirmation' => ['required'],
        ], [
            'password.min' => __('Password must be at least 8 characters.'),
        ]);

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
        $user->forceFill(['password' => $this->password])->save();

        $this->reset('password', 'password_confirmation', 'otpCode');
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
