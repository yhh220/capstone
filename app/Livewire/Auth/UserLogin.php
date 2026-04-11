<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class UserLogin extends Component
{
    // Tab toggle: true = Sign In, false = Register
    public bool $isLoginTab = true;

    // Sign In fields
    public string $loginEmail = '';
    public string $loginPassword = '';
    public bool $remember = false;

    // Register fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Password visibility toggle
    public bool $showPassword = false;

    /**
     * Switch between Sign In and Register tabs.
     */
    public function switchTab(bool $isLogin): void
    {
        $this->isLoginTab = $isLogin;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Handle user sign in with rate limiting (cybersecurity: brute-force protection).
     */
    public function login(): void
    {
        $this->validate([
            'loginEmail' => ['required', 'email'],
            'loginPassword' => ['required', 'string'],
        ]);

        // Rate limiting: max 5 attempts per minute per email
        $throttleKey = 'login:' . strtolower($this->loginEmail) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('loginEmail', "Too many login attempts. Please try again in {$seconds} seconds.");
            return;
        }

        if (!Auth::attempt([
            'email' => $this->loginEmail,
            'password' => $this->loginPassword,
        ], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);

            $this->addError('loginEmail', 'Invalid email or password.');
            return;
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

        // Regenerate session to prevent session fixation attacks
        session()->regenerate();

        $this->redirect('/', navigate: false);
    }

    /**
     * Handle user registration with strong password policy.
     *
     * CYBERSECURITY: Password is hashed automatically by the User model's
     * 'hashed' cast. We never store plaintext passwords.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => ['required'],
        ], [
            'password.min' => 'Password must be at least 8 characters.',
            'name.min' => 'Name must be at least 2 characters.',
        ]);

        // Create user — password is automatically hashed by the 'hashed' cast
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        // Auto-login after registration
        Auth::login($user);

        // Regenerate session to prevent session fixation attacks
        session()->regenerate();

        $this->redirect('/', navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.user-login')
            ->layout('layouts.app')
            ->title('Login — Win Win Car Studio');
    }
}
