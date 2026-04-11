<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class AdminLogin extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $showPassword = false;

    /**
     * Handle admin sign in.
     *
     * CYBERSECURITY:
     * - Rate limited to prevent brute-force attacks.
     * - After authentication, verifies the user has 'admin' role.
     * - Immediately logs out non-admin users to prevent privilege escalation.
     * - Session is regenerated to prevent session fixation attacks.
     */
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting: max 5 attempts per minute per IP
        $throttleKey = 'admin-login:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            RateLimiter::hit($throttleKey, 60);

            $this->addError('email', 'Invalid credentials.');
            return;
        }

        // Verify admin role — if not admin, logout immediately
        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            $this->addError('email', 'Access denied. This portal is for administrators only.');
            return;
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

        // Regenerate session to prevent session fixation attacks
        session()->regenerate();

        $this->redirect('/admin', navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.admin-login')
            ->layout('layouts.app')
            ->title('Admin Login — Win Win Car Studio');
    }
}
