<?php

namespace Tests\Feature;

use App\Livewire\Auth\UserLogin;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    /** Pretend Google returns this account on the callback. */
    private function fakeGoogle(string $id, string $email, string $name = 'Test'): void
    {
        // A provider is only "enabled" when its keys are present.
        config(['services.google.client_id' => 'test-id', 'services.google.client_secret' => 'test-secret']);

        $socialUser = (new SocialiteUser())->setRaw([])->map([
            'id' => $id, 'name' => $name, 'nickname' => null, 'email' => $email, 'avatar' => null,
        ]);

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($socialUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    /**
     * Regression test: Auth::login() regenerates the session id internally
     * (SessionGuard::updateSession() session-fixation hardening) before any
     * application code runs — every claimGuestCart() call site, including
     * the password-login path, was reading session()->getId() AFTER
     * Auth::login() and so was claiming against the wrong (already new) id.
     * A guest's cart was silently orphaned on every login path, not just
     * Google's — this just happened to be the one a real user hit first.
     */
    public function test_google_login_claims_the_guest_cart(): void
    {
        $product = Product::create([
            'name' => 'Speaker Kit', 'slug' => 'speaker-kit', 'price' => 250, 'stock' => 5, 'is_active' => true,
        ]);

        $this->get('/');
        $guestSessionId = session()->getId();
        CartItem::create(['session_id' => $guestSessionId, 'product_id' => $product->id, 'quantity' => 2]);

        $this->fakeGoogle('g-cart-1', 'cartguest@example.test');

        $cookieName = config('session.cookie');
        $this->withCookie($cookieName, $guestSessionId)
            ->get(route('social.callback', 'google'))
            ->assertRedirect(route('account'));

        $user = User::where('email', 'cartguest@example.test')->first();
        $this->assertSame(
            2,
            CartItem::where('user_id', $user->id)->where('product_id', $product->id)->value('quantity'),
        );
        $this->assertDatabaseMissing('cart_items', ['session_id' => $guestSessionId]);
    }

    public function test_google_login_creates_a_new_client_and_links_it(): void
    {
        $this->fakeGoogle('g-new-1', 'newcustomer@example.test', 'New Customer');

        $this->get(route('social.callback', 'google'))->assertRedirect(route('account'));

        $user = User::where('email', 'newcustomer@example.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('client', $user->role);                 // never admin from outside
        $this->assertNotNull($user->email_verified_at);           // provider-verified
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'g-new-1',
        ]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_login_restores_a_soft_deleted_account(): void
    {
        $user = User::create(['name' => 'Gone', 'email' => 'comeback@example.test', 'password' => 'secret']);
        $user->delete();
        $this->assertSoftDeleted($user);

        $this->fakeGoogle('g-restore-1', 'comeback@example.test');

        $this->get(route('social.callback', 'google'))->assertRedirect(route('account'));

        $this->assertNull($user->fresh()->deleted_at);            // reactivated, no duplicate-insert crash
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'g-restore-1',
        ]);
    }

    public function test_google_login_restores_an_already_linked_but_soft_deleted_account(): void
    {
        $user = User::create(['name' => 'Linked', 'email' => 'linked@example.test', 'password' => 'secret']);
        $user->socialAccounts()->create([
            'provider' => 'google', 'provider_id' => 'g-linked-1', 'provider_email' => 'linked@example.test',
        ]);
        $user->delete();
        $this->assertSoftDeleted($user);

        $this->fakeGoogle('g-linked-1', 'linked@example.test');

        $this->get(route('social.callback', 'google'))->assertRedirect(route('account'));

        $this->assertNull($user->fresh()->deleted_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_google_login_requires_otp_when_two_factor_is_enabled(): void
    {
        $user = User::create(['name' => 'Guarded', 'email' => 'guarded@example.test', 'password' => 'secret']);
        $user->forceFill(['two_factor_enabled' => true])->save();
        Notification::fake();

        $this->fakeGoogle('g-guarded-1', 'guarded@example.test');

        // The callback must NOT log the user in yet — it hands off to the
        // login page's OTP challenge instead.
        $this->get(route('social.callback', 'google'))->assertRedirect(route('login'));
        $this->assertGuest();

        $code = null;
        Notification::assertSentOnDemand(EmailOtp::class, function ($n) use (&$code) {
            $code = $n->code;
            return true;
        });
        $this->assertNotNull($code, 'an OTP must have been sent');

        // Loading the login page picks up the pending challenge from the session.
        $c = Livewire::test(UserLogin::class)
            ->assertSet('awaitingLoginOtp', true)
            ->assertSet('loginEmail', 'guarded@example.test');

        $c->set('loginOtpCode', '000000')->call('verifyLoginOtp');
        $this->assertGuest();

        $c->set('loginOtpCode', $code)->call('verifyLoginOtp');
        $this->assertAuthenticatedAs($user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
