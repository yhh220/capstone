<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
