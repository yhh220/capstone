<?php

namespace App\Http\Controllers;

use App\Exceptions\OtpSendFailedException;
use App\Models\CartItem;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\EmailOtpService;
use App\Support\Breadcrumbs;
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

        $wasTrashed = false;
        $user = $this->findOrCreateUser($provider, $socialUser, $wasTrashed);

        Breadcrumbs::push('auth', 'Social login', ['provider' => $provider, 'user' => $user->id]);

        // Login verification (email OTP) protects this account on the password
        // path — Google sign-in must honour the same promise instead of handing
        // out a session immediately, otherwise enabling it gives a false sense
        // of security for anyone who can complete the Google consent screen
        // (e.g. an already-signed-in shared device, or a compromised Google
        // account on the same email).
        if ($user->two_factor_enabled) {
            try {
                app(EmailOtpService::class)->send(EmailOtpService::PURPOSE_LOGIN, $user->email);
            } catch (OtpSendFailedException $e) {
                return redirect()->route('login')->withErrors(['loginEmail' => $e->getMessage()]);
            }

            session(['social_login_pending_email' => $user->email]);

            return redirect()->route('login');
        }

        // Captured before Auth::login() — SessionGuard::login() regenerates the
        // session id internally (session-fixation hardening) before this method
        // gets a chance to run, so reading session()->getId() any later than this
        // already returns the new id and claimGuestCart() would find nothing to
        // claim (the guest's cart is still tagged with this original id).
        $guestSessionId = session()->getId();

        Auth::login($user, remember: true);
        CartItem::claimGuestCart($guestSessionId, $user->id);
        request()->session()->regenerate();

        if ($wasTrashed) {
            session()->flash('success', __('Welcome back — your previously closed account has been reactivated.'));
        }

        return redirect()->intended(route('account'));
    }

    private function findOrCreateUser(string $provider, SocialiteUser $socialUser, bool &$wasTrashed = false): User
    {
        // 1. Already linked → that user, refreshing the cached email/avatar.
        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $account->update([
                'provider_email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            // withTrashed(): $account->user is a plain belongsTo, which is hidden by
            // the User SoftDeletes scope. Without this, a customer who deleted their
            // account and signs back in with the same provider hits a null-return
            // TypeError instead of being reactivated.
            $user = User::withTrashed()->findOrFail($account->user_id);

            if ($user->trashed()) {
                $user->restore();
                $wasTrashed = true;
            }

            return $user;
        }

        // 2. Same (provider-verified) email as an existing account → link them, so
        //    the customer can use either password or social login interchangeably.
        //    withTrashed() so a soft-deleted account still resolves (its email keeps
        //    the unique slot) instead of crashing on a duplicate insert.
        //    The `email` column's unique index doesn't exclude soft-deleted rows, so
        //    a brand-new account can never reuse this address anyway — reactivating
        //    is the only functional option. Surfaced to the user via $wasTrashed
        //    rather than left silent (the account-deletion page promises sign-in
        //    is permanently disabled).
        $email = $socialUser->getEmail();
        $user = $email ? User::withTrashed()->where('email', $email)->first() : null;

        // A previously-deleted customer signing back in → reactivate the account.
        if ($user && $user->trashed()) {
            $user->restore();
            $wasTrashed = true;
        }

        // 3. Brand-new customer. No password is set (NULL) — they sign in socially
        //    until they choose to add one from their account page.
        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Customer'),
                'email' => $email ?: "{$provider}_{$socialUser->getId()}@social.invalid",
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_email' => $email,
            'avatar' => $socialUser->getAvatar(),
        ]);

        return $user;
    }
}
