<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

/**
 * One-time setup flow to authorize this app to send mail as
 * winwincaraudio85@gmail.com via the Gmail API (see GmailApiTransport). Not
 * a customer-facing login — admin-guard only, and only needed again if the
 * refresh token is ever revoked.
 */
class GmailSendSetupController extends Controller
{
    public function connect()
    {
        abort_unless(auth('admin')->user()?->isAdmin(), 403);

        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/gmail.send'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirectUrl(route('gmail-send.callback'))
            ->redirect();
    }

    public function callback()
    {
        abort_unless(auth('admin')->user()?->isAdmin(), 403);

        try {
            $socialUser = Socialite::driver('google')
                ->redirectUrl(route('gmail-send.callback'))
                ->user();
        } catch (\Throwable $e) {
            return response('Google consent failed: ' . $e->getMessage(), 500);
        }

        if (! $socialUser->refreshToken) {
            return response(
                "No refresh token came back. This usually means the account already \n" .
                "authorized this app before without offline access. Go to \n" .
                "https://myaccount.google.com/permissions, remove this app's access, \n" .
                "then visit /gmail-send/connect again.",
                500
            );
        }

        return response(
            "Connected as: {$socialUser->getEmail()}\n\n" .
            "Refresh token (copy this into GMAIL_SEND_REFRESH_TOKEN in .env, both \n" .
            "locally and in Render's environment variables):\n\n" .
            $socialUser->refreshToken
        )->header('Content-Type', 'text/plain');
    }
}
