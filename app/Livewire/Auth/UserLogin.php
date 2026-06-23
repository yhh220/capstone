<?php

namespace App\Livewire\Auth;

use App\Models\CartItem;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

    // Keep the active tab in the URL (Register adds ?tab=false) so a refresh stays
    // on the same tab instead of snapping back to Sign In.
    protected $queryString = [
        'isLoginTab' => ['except' => true, 'as' => 'tab'],
    ];

    // Sign In fields. Password is NOT a public property — Livewire serializes
    // every public property into wire:snapshot (visible in page source / the
    // Network tab), so it's bound via Alpine-only state and passed into
    // login() as a #[\SensitiveParameter] method argument instead.
    public string $loginEmail = '';
    public bool $remember = false;

    // Register fields — same reasoning: password/password_confirmation stay
    // out of the public property list, passed as method params to register().
    public string $name = '';
    public string $email = '';

    // Email-OTP verification step (shown after a valid register submission)
    public bool   $awaitingOtp = false;
    public string $otpEmail    = '';
    public string $otpCode     = '';

    // Login 2FA step — shown after a valid password when the account has
    // email login-verification enabled. Credentials are confirmed by this
    // point; only the second factor remains, so this never reveals whether
    // 2FA is the reason a login didn't complete immediately to an attacker
    // who doesn't already have the right password.
    public bool   $awaitingLoginOtp = false;
    public string $loginOtpCode     = '';

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

        // SocialAuthController redirects here (instead of completing the login
        // itself) when the account has login verification enabled — the OTP was
        // already sent server-side, so just surface the same challenge screen
        // the password path uses. "remember" matches social login's own default.
        if ($pendingEmail = session()->pull('social_login_pending_email')) {
            $this->loginEmail       = $pendingEmail;
            $this->awaitingLoginOtp = true;
            $this->remember         = true;
        }
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
    public function login(#[\SensitiveParameter] string $loginPassword = ''): void
    {
        $v = Validator::make(
            ['loginEmail' => $this->loginEmail, 'loginPassword' => $loginPassword],
            ['loginEmail' => ['required', 'email'], 'loginPassword' => ['required', 'string']],
        );

        if ($v->fails()) {
            foreach ($v->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

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

        // A social-login-only account has no password — guide them to Google /
        // setting a password, rather than failing with "wrong password".
        $emailUser = User::where('email', $this->loginEmail)->first();
        if ($emailUser && ! $emailUser->hasPassword()) {
            $this->addError('loginEmail', __('This account uses Google sign-in. Please use the Google button, or set a password from your account page after signing in.'));
            return;
        }

        // Validate credentials without starting a session yet — an account with
        // login verification enabled still has to clear the OTP step below
        // before Auth::login() actually runs.
        if (! $emailUser || ! Hash::check($loginPassword, $emailUser->password)) {
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

        // Successful credential check — clear per-email failure counters only.
        // The IP-level counter ($ipKey) is intentionally NOT cleared: an attacker
        // who controls a valid account could otherwise reset the IP-wide spray
        // detection by interleaving their own successful logins between attempts
        // against victim accounts.
        Cache::forget($emailKey);
        Cache::forget($lockoutKey);
        Cache::forget($lockoutKey . ':expires');

        if ($emailUser->two_factor_enabled) {
            // Guard against OTP inbox flooding: someone who knows a victim's password
            // could repeatedly call login() to trigger unlimited OTP emails without
            // ever completing the second factor. Apply the same 60-second send
            // cooldown that resendLoginOtp() already enforces.
            $wait = app(EmailOtpService::class)->resendAvailableIn(EmailOtpService::PURPOSE_LOGIN, $emailUser->email);
            if ($wait <= 0) {
                try {
                    app(EmailOtpService::class)->send(EmailOtpService::PURPOSE_LOGIN, $emailUser->email);
                } catch (\App\Exceptions\OtpSendFailedException $e) {
                    $this->addError('loginEmail', $e->getMessage());
                    return;
                }
            }
            $this->awaitingLoginOtp = true;
            $this->loginOtpCode     = '';
            return;
        }

        $this->completeLogin($emailUser);
    }

    /**
     * Step 2 for accounts with login verification enabled: confirm the emailed
     * code, then finish the login that login() put on hold.
     */
    public function verifyLoginOtp(): void
    {
        $this->validate(['loginOtpCode' => ['required', 'digits:6']]);

        $user = User::where('email', $this->loginEmail)->first();

        if (! $user) {
            $this->awaitingLoginOtp = false;
            $this->addError('loginEmail', __('Something went wrong. Please sign in again.'));
            return;
        }

        if (! app(EmailOtpService::class)->verify(EmailOtpService::PURPOSE_LOGIN, $user->email, $this->loginOtpCode)) {
            $this->addError('loginOtpCode', __('Invalid or expired code. Please try again.'));
            return;
        }

        $this->completeLogin($user);
    }

    /**
     * Resend the login code (throttled to once per 60s by EmailOtpService).
     */
    public function resendLoginOtp(): void
    {
        if (! $this->awaitingLoginOtp) {
            return;
        }

        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_LOGIN, $this->loginEmail);

        if ($wait > 0) {
            $this->addError('loginOtpCode', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        try {
            $otp->send(EmailOtpService::PURPOSE_LOGIN, $this->loginEmail);
        } catch (\App\Exceptions\OtpSendFailedException $e) {
            $this->addError('loginOtpCode', $e->getMessage());
            return;
        }

        session()->flash('otp_resent', __('A new code has been sent to your email.'));
    }

    /**
     * Abandon the login-verification step and return to the sign-in form.
     */
    public function cancelLoginOtp(): void
    {
        app(EmailOtpService::class)->clear(EmailOtpService::PURPOSE_LOGIN, $this->loginEmail);
        $this->awaitingLoginOtp = false;
        $this->loginOtpCode     = '';
        $this->resetErrorBag();
    }

    private function completeLogin(User $user): void
    {
        Auth::login($user, $this->remember);
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

    /**
     * Step 1 of registration: validate input, stash the pending account (password
     * encrypted, NOT plaintext) in the cache, email a 6-digit OTP, and switch the
     * UI to the verification step. The user row is only created once the code is
     * confirmed, so abandoned sign-ups never leave an orphan account and the email
     * stays free to register.
     */
    public function register(
        #[\SensitiveParameter] string $password = '',
        #[\SensitiveParameter] string $passwordConfirmation = '',
    ): void {
        $this->protectAgainstSpam();

        $v = Validator::make(
            [
                'name'                  => $this->name,
                'email'                 => $this->email,
                'password'              => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'name'                  => ['required', 'string', 'min:2', 'max:255'],
                // Only ACTIVE accounts block the email; a soft-deleted one is treated as
                // available and gets reactivated on OTP confirmation (returning customer).
                'email'                 => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
                'password'              => ['required', 'confirmed', Password::defaults()],
                'password_confirmation' => ['required'],
            ],
            [
                'password.min' => __('Password must be at least 8 characters.'),
                'name.min'     => __('Name must be at least 2 characters.'),
            ],
        );

        if ($v->fails()) {
            foreach ($v->errors()->messages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

        $validated = $v->validated();

        // Throttle by IP so registration can't be abused to mass-send OTP emails
        // (each valid submission dispatches a verification email).
        $rlKey = 'register:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rlKey, 5)) {
            $seconds = RateLimiter::availableIn($rlKey);
            $this->addError('email', __('Too many sign-up attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]));
            return;
        }
        RateLimiter::hit($rlKey, 600); // max 5 per 10 minutes per IP

        Cache::put($this->pendingKey($validated['email']), [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Crypt::encryptString($validated['password']),
        ], EmailOtpService::TTL);

        try {
            app(EmailOtpService::class)->send(EmailOtpService::PURPOSE_REGISTER, $validated['email']);
        } catch (\App\Exceptions\OtpSendFailedException $e) {
            $this->addError('email', $e->getMessage());
            return;
        }

        $this->otpEmail    = $validated['email'];
        $this->awaitingOtp = true;
        $this->otpCode     = '';
        $this->resetErrorBag();
    }

    /**
     * Step 2 of registration: confirm the OTP, then create the (verified) account.
     */
    public function verifyRegistrationOtp(): void
    {
        $this->validate(['otpCode' => ['required', 'digits:6']]);

        $otp = app(EmailOtpService::class);

        if (! $otp->verify(EmailOtpService::PURPOSE_REGISTER, $this->otpEmail, $this->otpCode)) {
            $this->addError('otpCode', __('Invalid or expired code. Please try again.'));
            return;
        }

        $pending = Cache::get($this->pendingKey($this->otpEmail));

        if (! $pending) {
            $this->awaitingOtp = false;
            $this->isLoginTab  = false;
            $this->addError('email', __('Your session expired. Please register again.'));
            return;
        }

        // Guard against a race: someone may have registered this email meanwhile.
        if (User::where('email', $pending['email'])->exists()) {
            Cache::forget($this->pendingKey($this->otpEmail));
            $this->awaitingOtp = false;
            $this->isLoginTab  = true;
            $this->addError('loginEmail', __('This account already exists. Please sign in.'));
            return;
        }

        // A returning customer whose account was soft-deleted → reactivate it with
        // the freshly-entered details (no active account exists past the check above,
        // so any match here is soft-deleted). Otherwise create a fresh 'client'.
        // forceCreate/forceFill set 'role' explicitly (not mass-assignable); the
        // 'hashed' cast hashes the decrypted plaintext password exactly once.
        $user = User::onlyTrashed()->where('email', $pending['email'])->first();
        $wasTrashed = false;

        if ($user) {
            $user->restore();
            $wasTrashed = true;
            $user->forceFill([
                'name'              => $pending['name'],
                'password'          => Crypt::decryptString($pending['password']),
                'email_verified_at' => now(),
            ])->save();
        } else {
            $user = User::forceCreate([
                'name'              => $pending['name'],
                'email'             => $pending['email'],
                'password'          => Crypt::decryptString($pending['password']),
                'role'              => 'client',
                'email_verified_at' => now(),
            ]);
        }

        Cache::forget($this->pendingKey($this->otpEmail));

        Auth::login($user);
        CartItem::claimGuestCart(session()->getId(), Auth::id());
        session()->regenerate();

        if ($wasTrashed) {
            session()->flash('success', __('Welcome back — your previously closed account has been reactivated.'));
        }

        $this->redirect(session()->pull('url.intended', '/'), navigate: false);
    }

    /**
     * Resend the registration code (throttled to once per 60s by EmailOtpService).
     */
    public function resendRegistrationOtp(): void
    {
        if (! $this->awaitingOtp || $this->otpEmail === '') {
            return;
        }

        $otp  = app(EmailOtpService::class);
        $wait = $otp->resendAvailableIn(EmailOtpService::PURPOSE_REGISTER, $this->otpEmail);

        if ($wait > 0) {
            $this->addError('otpCode', __('Please wait :seconds seconds before requesting a new code.', ['seconds' => $wait]));
            return;
        }

        if (! Cache::has($this->pendingKey($this->otpEmail))) {
            $this->awaitingOtp = false;
            $this->isLoginTab  = false;
            $this->addError('email', __('Your session expired. Please register again.'));
            return;
        }

        try {
            $otp->send(EmailOtpService::PURPOSE_REGISTER, $this->otpEmail);
        } catch (\App\Exceptions\OtpSendFailedException $e) {
            $this->addError('otpCode', $e->getMessage());
            return;
        }

        session()->flash('otp_resent', __('A new code has been sent to your email.'));
    }

    /**
     * Abandon the OTP step and return to the register form.
     */
    public function cancelOtp(): void
    {
        if ($this->otpEmail !== '') {
            app(EmailOtpService::class)->clear(EmailOtpService::PURPOSE_REGISTER, $this->otpEmail);
            Cache::forget($this->pendingKey($this->otpEmail));
        }

        $this->awaitingOtp = false;
        $this->otpCode     = '';
        $this->resetErrorBag();
    }

    private function pendingKey(string $email): string
    {
        return 'pending_registration:' . strtolower(trim($email));
    }

    public function render()
    {
        return view('livewire.auth.user-login')
            ->layout('layouts.app')
            ->title('Login — Win Win Car Studio');
    }
}
