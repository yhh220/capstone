<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\User;
use App\Support\SocialLogin;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /** Send the visitor off to the provider's consent screen. */
    public function redirect(string $provider)
    {
        abort_unless(SocialLogin::isEnabled($provider), 404);

        return Socialite::driver($provider)
            ->redirectUrl(route('social.callback', $provider))
            ->redirect();
    }

    /** Handle the provider's callback: find-or-create the user, then log in. */
    public function callback(string $provider)
    {
        abort_unless(SocialLogin::isEnabled($provider), 404);

        try {
            $socialUser = Socialite::driver($provider)
                ->redirectUrl(route('social.callback', $provider))
                ->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'loginEmail' => __('We could not sign you in with :provider. Please try again.', [
                    'provider' => SocialLogin::PROVIDERS[$provider] ?? ucfirst($provider),
                ]),
            ]);
        }

        $user = $this->findOrCreateUser($provider, $socialUser);

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended(route('account'));
    }

    private function findOrCreateUser(string $provider, SocialiteUser $socialUser): User
    {
        // 1. Already linked → that user, refreshing the cached email/avatar.
        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $account->update([
                'provider_email' => $socialUser->getEmail(),
                'avatar'         => $socialUser->getAvatar(),
            ]);

            return $account->user;
        }

        // 2. Same (provider-verified) email as an existing account → link them, so
        //    the customer can use either password or social login interchangeably.
        //    withTrashed() so a soft-deleted account still resolves (its email keeps
        //    the unique slot) instead of crashing on a duplicate insert.
        $email = $socialUser->getEmail();
        $user  = $email ? User::withTrashed()->where('email', $email)->first() : null;

        // A previously-deleted customer signing back in → reactivate the account.
        if ($user && $user->trashed()) {
            $user->restore();
        }

        // 3. Brand-new customer. No password is set (NULL) — they sign in socially
        //    until they choose to add one from their account page.
        if (! $user) {
            $user = User::create([
                'name'  => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Customer'),
                'email' => $email ?: "{$provider}_{$socialUser->getId()}@social.invalid",
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $user->socialAccounts()->create([
            'provider'       => $provider,
            'provider_id'    => $socialUser->getId(),
            'provider_email' => $email,
            'avatar'         => $socialUser->getAvatar(),
        ]);

        return $user;
    }
}
