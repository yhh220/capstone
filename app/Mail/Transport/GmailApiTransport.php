<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

/**
 * Sends mail via the Gmail REST API (HTTPS) instead of raw SMTP. Confirmed via
 * a live connectivity probe that Render silently drops outbound connections
 * on ports 587/465/25 — Gmail SMTP can never work from this host. The Gmail
 * API reaches the same account over HTTPS, which is never blocked.
 *
 * Auth is a long-lived OAuth refresh token for winwincaraudio85@gmail.com
 * (scope: gmail.send), obtained once via the /gmail-send/connect flow.
 * Access tokens are minted from it on demand and cached for ~50 minutes.
 */
class GmailApiTransport extends AbstractTransport
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $refreshToken,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $raw = rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($message->toString())), '=');

        $response = Http::withToken($this->accessToken())
            ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $raw,
            ]);

        if ($response->failed()) {
            throw new TransportException('Gmail API send failed: ' . $response->body());
        }
    }

    private function accessToken(): string
    {
        return Cache::remember('gmail_api_access_token', 3000, function () {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type'    => 'refresh_token',
            ]);

            if ($response->failed()) {
                throw new TransportException('Failed to refresh Gmail API access token: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    public function __toString(): string
    {
        return 'gmail+api';
    }
}
