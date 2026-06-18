<?php

namespace App\Services;

use App\Notifications\EmailOtp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Cache-backed one-time-password helper shared by registration verification and
 * password reset. Codes are never stored in plaintext (hashed in cache), expire
 * after 10 minutes, are capped at 5 verification attempts, and resends are
 * throttled to once per 60 seconds — mirroring the security posture of the
 * login lockout and the booking/order trackers.
 */
class EmailOtpService
{
    /** Code lifetime in seconds. */
    public const TTL = 600;

    /** Wrong-code attempts before the code is invalidated. */
    public const MAX_ATTEMPTS = 5;

    /** Minimum seconds between resends for the same purpose+email. */
    public const RESEND_COOLDOWN = 60;

    public const PURPOSE_REGISTER = 'register';
    public const PURPOSE_RESET    = 'pwreset';

    /**
     * Generate a fresh 6-digit code, store it hashed, and email it. Returns the
     * plaintext code only so callers can optionally surface it in demo mode.
     */
    public function send(string $purpose, string $email): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put($this->codeKey($purpose, $email), Hash::make($code), self::TTL);
        Cache::put($this->attemptsKey($purpose, $email), 0, self::TTL);

        Notification::route('mail', $this->normalize($email))
            ->notify(new EmailOtp($code, $purpose));

        RateLimiter::hit($this->resendKey($purpose, $email), self::RESEND_COOLDOWN);

        return $code;
    }

    /** Seconds until another send is allowed (0 = allowed now). */
    public function resendAvailableIn(string $purpose, string $email): int
    {
        $key = $this->resendKey($purpose, $email);

        return RateLimiter::tooManyAttempts($key, 1)
            ? RateLimiter::availableIn($key)
            : 0;
    }

    /**
     * Verify a submitted code. On success the code is consumed (single use).
     * Wrong codes count toward the attempt cap; exhausting it invalidates the code.
     */
    public function verify(string $purpose, string $email, string $code): bool
    {
        $hash = Cache::get($this->codeKey($purpose, $email));

        if (! $hash) {
            return false; // expired or never issued
        }

        $attempts = (int) Cache::get($this->attemptsKey($purpose, $email), 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->clear($purpose, $email);
            return false;
        }

        if (! Hash::check($code, $hash)) {
            Cache::put($this->attemptsKey($purpose, $email), $attempts + 1, self::TTL);
            return false;
        }

        $this->clear($purpose, $email);

        return true;
    }

    /** Whether a live (unexpired) code is currently issued. */
    public function hasActiveCode(string $purpose, string $email): bool
    {
        return Cache::has($this->codeKey($purpose, $email));
    }

    public function clear(string $purpose, string $email): void
    {
        Cache::forget($this->codeKey($purpose, $email));
        Cache::forget($this->attemptsKey($purpose, $email));
    }

    private function normalize(string $email): string
    {
        return strtolower(trim($email));
    }

    private function codeKey(string $purpose, string $email): string
    {
        return "otp:{$purpose}:" . $this->normalize($email);
    }

    private function attemptsKey(string $purpose, string $email): string
    {
        return "otp_attempts:{$purpose}:" . $this->normalize($email);
    }

    private function resendKey(string $purpose, string $email): string
    {
        return "otp_resend:{$purpose}:" . $this->normalize($email);
    }
}
