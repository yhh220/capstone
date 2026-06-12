<?php

namespace App\Livewire\Auth;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class UserLogin extends Component
{
    use \App\Livewire\Concerns\SetsSeo;
    use UsesSpamProtection;

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

    // Honeypot for Register form — powered by spatie/laravel-honeypot
    public HoneypotData $honeypotData;

    // Password visibility toggle
    public bool $showPassword = false;

    // Progressive lockout thresholds: [attempts => lockout_seconds]
    // Apple-style: 5 → 30s, 10 → 60s, 15 → 300s (5min), 20 → 900s (15min), 25+ → 3600s (1hr)
    private const LOCKOUT_TIERS = [
        5  => 30,
        10 => 60,
        15 => 300,
        20 => 900,
        25 => 3600,
    ];

    // IP-level block after 30 attempts (bot/spray attack detection)
    private const IP_BLOCK_THRESHOLD = 30;
    private const IP_BLOCK_SECONDS   = 3600;

    public function mount(): void
    {
        $this->honeypotData = new HoneypotData();
        $this->setSeo(
            title: __('Login'),
            description: 'Sign in or create an account to shop and track your orders at Win Win Car Audio.',
        );
    }

    public function switchTab(bool $isLogin): void
    {
        $this->isLoginTab = $isLogin;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Handle sign in with Apple-style progressive lockout.
     * Tier 1: 5 fails  → 30 sec wait
     * Tier 2: 10 fails → 1 min wait
     * Tier 3: 15 fails → 5 min wait
     * Tier 4: 20 fails → 15 min wait
     * Tier 5: 25 fails → 1 hr wait
     * IP block: 30 fails from same IP → 1 hr IP-level block
     */
    public function login(): void
    {
        $this->validate([
            'loginEmail' => ['required', 'email'],
            'loginPassword' => ['required', 'string'],
        ]);

        $ip         = request()->ip();
        $emailKey   = 'login_fails:email:' . strtolower($this->loginEmail);
        $ipKey      = 'login_fails:ip:' . $ip;
        $ipBlockKey = 'login_block:ip:' . $ip;

        // Check IP-level block first (bot/spray detection)
        if (Cache::has($ipBlockKey)) {
            $remaining = Cache::get($ipBlockKey . ':expires', now()->timestamp) - now()->timestamp;
            $minutes = max(1, (int) ceil($remaining / 60));
            $this->addError('loginEmail', __('Unusual activity detected from your network. Please try again in :minutes minutes.', ['minutes' => $minutes]));
            return;
        }

        // Check per-email progressive lockout
        $emailFails  = (int) Cache::get($emailKey, 0);
        $lockoutKey  = 'login_lockout:' . strtolower($this->loginEmail) . ':' . $ip;

        if (Cache::has($lockoutKey)) {
            $seconds = max(1, Cache::get($lockoutKey . ':expires', now()->timestamp) - now()->timestamp);
            $this->addError('loginEmail', $this->lockoutMessage((int) $seconds));
            return;
        }

        // Attempt authentication
        if (!Auth::attempt([
            'email'    => $this->loginEmail,
            'password' => $this->loginPassword,
        ], $this->remember)) {
            // Increment failure counters
            $emailFails++;
            $ipFails = (int) Cache::get($ipKey, 0) + 1;

            Cache::put($emailKey, $emailFails, now()->addHours(2));
            Cache::put($ipKey,    $ipFails,    now()->addHours(2));

            // IP-level block for high-volume attacks
            if ($ipFails >= self::IP_BLOCK_THRESHOLD) {
                $expires = now()->addSeconds(self::IP_BLOCK_SECONDS)->timestamp;
                Cache::put($ipBlockKey,              true,    now()->addSeconds(self::IP_BLOCK_SECONDS));
                Cache::put($ipBlockKey . ':expires', $expires, now()->addSeconds(self::IP_BLOCK_SECONDS));
                $this->addError('loginEmail', __('Unusual activity detected from your network. Please try again in 60 minutes.'));
                return;
            }

            // Progressive lockout based on per-email failure count
            $lockoutSeconds = $this->lockoutSecondsFor($emailFails);
            if ($lockoutSeconds > 0) {
                $expires = now()->addSeconds($lockoutSeconds)->timestamp;
                Cache::put($lockoutKey,              true,   now()->addSeconds($lockoutSeconds));
                Cache::put($lockoutKey . ':expires', $expires, now()->addSeconds($lockoutSeconds));
                $this->addError('loginEmail', $this->lockoutMessage($lockoutSeconds));
                return;
            }

            $remaining = 5 - ($emailFails % 5);
            if ($remaining > 0 && $remaining < 5) {
                $this->addError('loginEmail', __('Invalid email or password. :n attempt(s) remaining before lockout.', ['n' => $remaining]));
            } else {
                $this->addError('loginEmail', __('Invalid email or password.'));
            }
            return;
        }

        // Successful login — clear all counters
        Cache::forget($emailKey);
        Cache::forget($ipKey);
        Cache::forget($lockoutKey);
        Cache::forget($lockoutKey . ':expires');

        CartItem::claimGuestCart(session()->getId(), Auth::id());
        session()->regenerate();

        $this->redirect(session()->pull('url.intended', '/'), navigate: false);
    }

    private function lockoutSecondsFor(int $fails): int
    {
        $seconds = 0;
        foreach (self::LOCKOUT_TIERS as $threshold => $wait) {
            if ($fails >= $threshold) {
                $seconds = $wait;
            }
        }
        return $seconds;
    }

    private function lockoutMessage(int $seconds): string
    {
        if ($seconds >= 3600) {
            return __('Too many failed attempts. Please try again in 1 hour.');
        }
        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);
            return __('Too many failed attempts. Please try again in :minutes minutes.', ['minutes' => $minutes]);
        }
        return __('Too many failed attempts. Please wait :seconds seconds.', ['seconds' => $seconds]);
    }

    public function register(): void
    {
        $this->protectAgainstSpam();

        $validated = $this->validate([
            'name'                  => ['required', 'string', 'min:2', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers()->symbols(),
            ],
            'password_confirmation' => ['required'],
        ], [
            'password.min' => __('Password must be at least 8 characters.'),
            'name.min'     => __('Name must be at least 2 characters.'),
        ]);

        // forceCreate so 'role' is set explicitly (it is not mass-assignable).
        // Public registration is always a 'client' — never staff/admin.
        $user = User::forceCreate([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => 'client',
        ]);

        Auth::login($user);
        CartItem::claimGuestCart(session()->getId(), Auth::id());
        session()->regenerate();

        $this->redirect(session()->pull('url.intended', '/'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.user-login')
            ->layout('layouts.app')
            ->title('Login — Win Win Car Studio');
    }
}
