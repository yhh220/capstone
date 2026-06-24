<?php

namespace Tests\Feature;

use App\Livewire\Auth\UserLogin;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\EmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Regression test: Auth::login() regenerates the session id internally
 * (SessionGuard::updateSession() session-fixation hardening) before any
 * application code runs. Every claimGuestCart() call site read
 * session()->getId() AFTER calling Auth::login() and so claimed against
 * the wrong (already-regenerated) id — a guest's cart was silently
 * orphaned on every login and registration path, not just social login.
 */
class GuestCartClaimOnLoginTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        return Product::create([
            'name' => 'Speaker Kit', 'slug' => 'speaker-kit', 'price' => 250, 'stock' => 5, 'is_active' => true,
        ]);
    }

    public function test_password_login_claims_the_guest_cart(): void
    {
        $user = User::create(['name' => 'C', 'email' => 'c@example.test', 'password' => 'password', 'role' => 'client']);
        $product = $this->product();

        $guestSessionId = session()->getId();
        CartItem::create(['session_id' => $guestSessionId, 'product_id' => $product->id, 'quantity' => 3]);

        Livewire::test(UserLogin::class)
            ->set('loginEmail', 'c@example.test')
            ->call('login', 'password');

        $this->assertSame(
            3,
            CartItem::where('user_id', $user->id)->where('product_id', $product->id)->value('quantity'),
        );
        $this->assertDatabaseMissing('cart_items', ['session_id' => $guestSessionId]);
    }

    public function test_registration_claims_the_guest_cart(): void
    {
        $product = $this->product();

        $guestSessionId = session()->getId();
        CartItem::create(['session_id' => $guestSessionId, 'product_id' => $product->id, 'quantity' => 1]);

        Notification::fake();

        $component = Livewire::test(UserLogin::class);
        $this->travel(2)->seconds(); // clear the honeypot's minimum-fill-time gate

        $component
            ->set('name', 'New Customer')
            ->set('email', 'newcustomer@example.test')
            ->call('register', 'Password123!', 'Password123!');

        $code = null;
        Notification::assertSentOnDemand(EmailOtp::class, function ($n) use (&$code) {
            $code = $n->code;

            return true;
        });
        $this->assertNotNull($code, 'a registration OTP must have been sent');

        Livewire::test(UserLogin::class)
            ->set('otpEmail', 'newcustomer@example.test')
            ->set('awaitingOtp', true)
            ->set('otpCode', $code)
            ->call('verifyRegistrationOtp');

        $user = User::where('email', 'newcustomer@example.test')->first();
        $this->assertNotNull($user);
        $this->assertSame(
            1,
            CartItem::where('user_id', $user->id)->where('product_id', $product->id)->value('quantity'),
        );
        $this->assertDatabaseMissing('cart_items', ['session_id' => $guestSessionId]);
    }
}
