<?php

namespace Tests\Feature;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\UserLogin;
use App\Livewire\ProfilePage;
use App\Models\User;
use App\Notifications\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    private string $email = 'flowtest@example.com';
    private string $pass  = 'Passw0rd!@#';

    private function captureOtp(): string
    {
        $code = null;
        Notification::assertSentOnDemand(EmailOtp::class, function ($n) use (&$code) {
            $code = $n->code;
            return true;
        });
        return $code;
    }

    public function test_registration_requires_email_otp_before_creating_account(): void
    {
        // The honeypot rejects instant (bot-speed) submissions; a real user passes
        // its timing gate. Disable it here so we can exercise the OTP flow directly.
        config(['honeypot.enabled' => false]);

        Notification::fake();

        $c = Livewire::test(UserLogin::class)
            ->set('isLoginTab', false)
            ->set('name', 'Flow Test')
            ->set('email', $this->email)
            ->set('password', $this->pass)
            ->set('password_confirmation', $this->pass)
            ->call('register');

        $c->assertSet('awaitingOtp', true);
        $this->assertFalse(User::where('email', $this->email)->exists(), 'user must not exist before OTP');

        $code = $this->captureOtp();

        // Wrong code → still no account
        $c->set('otpCode', '000000')->call('verifyRegistrationOtp');
        $this->assertFalse(User::where('email', $this->email)->exists());

        // Correct code → verified client account, logged in
        $c->set('otpCode', $code)->call('verifyRegistrationOtp');
        $u = User::where('email', $this->email)->first();
        $this->assertNotNull($u);
        $this->assertSame('client', $u->role);
        $this->assertNotNull($u->email_verified_at);
        $this->assertTrue(Auth::check());
    }

    public function test_registration_reactivates_a_soft_deleted_account(): void
    {
        config(['honeypot.enabled' => false]);
        Notification::fake();

        // A previous account with this email was deleted.
        $old = User::forceCreate([
            'name' => 'Old Name', 'email' => $this->email,
            'password' => 'OldPass1!@#', 'role' => 'client', 'email_verified_at' => now(),
        ]);
        $old->delete();
        $this->assertSoftDeleted($old);

        $c = Livewire::test(UserLogin::class)
            ->set('isLoginTab', false)
            ->set('name', 'New Name')
            ->set('email', $this->email)
            ->set('password', $this->pass)
            ->set('password_confirmation', $this->pass)
            ->call('register')
            ->assertHasNoErrors()              // a soft-deleted email is NOT "taken"
            ->assertSet('awaitingOtp', true);

        $c->set('otpCode', $this->captureOtp())->call('verifyRegistrationOtp');

        // The same row is reactivated with the new details — no duplicate insert.
        $this->assertSame(1, User::withTrashed()->where('email', $this->email)->count());
        $fresh = $old->fresh();
        $this->assertNull($fresh->deleted_at);
        $this->assertSame('New Name', $fresh->name);
        $this->assertTrue(Hash::check($this->pass, $fresh->password));
        $this->assertTrue(Auth::check());
    }

    public function test_sign_in_and_sign_out(): void
    {
        $u = $this->makeUser();

        Livewire::test(UserLogin::class)
            ->set('loginEmail', $this->email)
            ->set('loginPassword', 'totally-wrong')
            ->call('login');
        $this->assertFalse(Auth::check(), 'wrong password must not sign in');

        Livewire::test(UserLogin::class)
            ->set('loginEmail', $this->email)
            ->set('loginPassword', $this->pass)
            ->call('login');
        $this->assertTrue(Auth::check());
        $this->assertSame($u->id, Auth::id());
    }

    public function test_change_password(): void
    {
        $u = $this->makeUser();
        $this->actingAs($u);

        Livewire::test(ProfilePage::class)
            ->call('updatePassword', $this->pass, 'NewPass1!@#', 'NewPass1!@#');

        $this->assertTrue(Hash::check('NewPass1!@#', $u->fresh()->password));
    }

    public function test_forgot_password_otp_reset(): void
    {
        $u = $this->makeUser();
        Notification::fake();
        config(['honeypot.enabled' => false]); // honeypot rejects instant submits; a real user passes

        $fp = Livewire::test(ForgotPassword::class)
            ->set('email', $this->email)
            ->call('sendCode')
            ->assertSet('step', 2);

        $code = $this->captureOtp();

        $fp->set('otpCode', $code)
            ->set('password', 'Reset1!@#xyz')
            ->set('password_confirmation', 'Reset1!@#xyz')
            ->call('resetPassword')
            ->assertSet('step', 3);

        $this->assertTrue(Hash::check('Reset1!@#xyz', $u->fresh()->password));
    }

    public function test_delete_account_is_soft_delete(): void
    {
        $u = $this->makeUser();
        $this->actingAs($u);

        Livewire::test(ProfilePage::class)
            ->call('deleteAccount', $this->pass);

        $this->assertFalse(User::where('email', $this->email)->exists(), 'hidden from normal queries');
        $this->assertTrue(
            User::withTrashed()->where('email', $this->email)->whereNotNull('deleted_at')->exists(),
            'row retained in DB with deleted_at'
        );
        $this->assertFalse(Auth::check());
    }

    private function makeUser(): User
    {
        return User::forceCreate([
            'name'              => 'Flow Test',
            'email'             => $this->email,
            'password'          => $this->pass, // hashed cast
            'role'              => 'client',
            'email_verified_at' => now(),
        ]);
    }
}
