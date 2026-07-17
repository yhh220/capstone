<?php

namespace Tests\Feature;

use App\Livewire\Auth\UserLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Progressive login lockout (Apple-style tiers).
 *
 * Intended behavior: within each tier of 5 the "N attempt(s) remaining"
 * countdown decrements 4 → 3 → 2 → 1, the 5th/10th/15th/... failure locks
 * the email+IP pair for an escalating duration (30s / 60s / 5min / 15min /
 * 1hr), and after a lockout expires the countdown starts over for the next
 * tier. Regression guard: lockoutSecondsFor() used to return a non-zero
 * wait for EVERY failure past the 5th, so the countdown never reappeared
 * and each single failure re-locked the account immediately.
 */
class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    private function attempt(Testable $component, string $password = 'wrong-password'): string
    {
        $component->call('login', $password);

        return (string) $component->instance()->getErrorBag()->first('loginEmail');
    }

    private function freshComponent(string $email): Testable
    {
        return Livewire::test(UserLogin::class)->set('loginEmail', $email);
    }

    public function test_wrong_password_counts_down_then_locks_on_the_fifth_failure(): void
    {
        User::create(['name' => 'V', 'email' => 'victim@example.test', 'password' => 'correct-password', 'role' => 'client']);

        $c = $this->freshComponent('victim@example.test');

        $this->assertStringContainsString('4 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('3 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('2 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('1 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('wait 30 seconds', $this->attempt($c));
    }

    public function test_nonexistent_account_gets_the_same_countdown(): void
    {
        // The colleague's test case: gibberish email, repeated attempts. The
        // countdown must decrement (same messages as a real account, so account
        // existence is not enumerable) — never stay stuck at 4.
        $c = $this->freshComponent('adadasd@adadadasdasdasdad');

        $this->assertStringContainsString('4 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('3 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('2 attempt(s) remaining', $this->attempt($c));
    }

    public function test_countdown_restarts_after_lockout_and_next_tier_locks_longer(): void
    {
        User::create(['name' => 'V', 'email' => 'victim@example.test', 'password' => 'correct-password', 'role' => 'client']);

        $c = $this->freshComponent('victim@example.test');

        for ($i = 0; $i < 4; $i++) {
            $this->attempt($c);
        }
        $this->assertStringContainsString('wait 30 seconds', $this->attempt($c)); // 5th failure → tier 1

        // While locked, attempts are rejected without burning the counter.
        $this->assertStringContainsString('Too many failed attempts', $this->attempt($c));

        $this->travel(31)->seconds();

        // Failures 6–9: countdown runs again instead of instantly re-locking.
        $this->assertStringContainsString('4 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('3 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('2 attempt(s) remaining', $this->attempt($c));
        $this->assertStringContainsString('1 attempt(s) remaining', $this->attempt($c));

        // 10th failure → tier 2 (60s → "1 minutes").
        $this->assertStringContainsString('try again in 1 minutes', $this->attempt($c));
    }

    public function test_successful_login_resets_the_email_counter(): void
    {
        User::create(['name' => 'V', 'email' => 'victim@example.test', 'password' => 'correct-password', 'role' => 'client']);

        $c = $this->freshComponent('victim@example.test');
        $this->attempt($c);
        $this->attempt($c);

        $c->call('login', 'correct-password');
        $this->assertAuthenticated();

        auth()->logout();

        $c2 = $this->freshComponent('victim@example.test');
        $this->assertStringContainsString('4 attempt(s) remaining', $this->attempt($c2));
    }
}
