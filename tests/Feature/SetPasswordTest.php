<?php

namespace Tests\Feature;

use App\Livewire\Auth\UserLogin;
use App\Livewire\CheckoutPage;
use App\Livewire\ProfilePage;
use App\Models\User;
use App\Notifications\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class SetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** A social-login-style account: no password. */
    private function socialUser(string $email = 'social@example.test'): User
    {
        return User::forceCreate([
            'name' => 'Social', 'email' => $email, 'role' => 'client', 'email_verified_at' => now(),
        ]);
    }

    public function test_password_login_on_a_passwordless_account_is_guided_not_failed(): void
    {
        $user = $this->socialUser();
        $this->assertFalse($user->hasPassword());

        Livewire::test(UserLogin::class)
            ->set('loginEmail', $user->email)
            ->call('login', 'anything123!')
            ->assertHasErrors('loginEmail');

        $this->assertGuest();
    }

    public function test_passwordless_user_is_redirected_from_checkout_to_set_a_password(): void
    {
        $user = $this->socialUser();

        Livewire::actingAs($user)
            ->test(CheckoutPage::class)
            ->assertRedirect(route('profile'));
    }

    public function test_otp_gated_set_password_gives_the_account_a_usable_password(): void
    {
        Notification::fake();
        $user = $this->socialUser();

        $page = Livewire::actingAs($user)
            ->test(ProfilePage::class)
            ->call('sendSetPasswordCode')
            ->assertSet('settingPassword', true);

        $code = null;
        Notification::assertSentOnDemand(EmailOtp::class, function ($n) use (&$code) {
            $code = $n->code;

            return true;
        });

        $page->call('confirmSetPassword', $code, 'BrandNew1!@#', 'BrandNew1!@#')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue($user->hasPassword());
        $this->assertTrue(Hash::check('BrandNew1!@#', $user->password));
    }
}
