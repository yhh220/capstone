<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * The only class that talks to the Stripe SDK. Hosted Checkout (redirect) in
 * TEST mode: a server-created session per order, paid on Stripe's page, then
 * confirmed by webhook and/or the success-URL return — never by the redirect
 * alone. Feature tests mock this service, so no test ever hits the network.
 */
class StripeCheckoutService
{
    /** Stripe Checkout's minimum session lifetime. */
    public const SESSION_MINUTES = 30;

    /**
     * Extra minutes past the Stripe session expiry before the order expiry job
     * may cancel + restock, so a payment finishing at the session's last second
     * (plus webhook latency) can never land on a cancelled order.
     */
    public const EXPIRY_GRACE_MINUTES = 2;

    private ?StripeClient $client = null;

    /** Logged-once guard for the misconfiguration warning (per request). */
    private static bool $warnedMisconfigured = false;

    /**
     * Stripe is used only when the admin switched PAYMENT_MODE to 'stripe' AND
     * a test-mode secret is configured. Only sk_test_ keys are accepted — this
     * codebase must never be able to charge real money. Anything else falls
     * back to the demo flow.
     */
    public function enabled(): bool
    {
        if (setting('PAYMENT_MODE', 'demo') !== 'stripe') {
            return false;
        }

        $secret = (string) config('services.stripe.secret');

        if (! str_starts_with($secret, 'sk_test_')) {
            if (! self::$warnedMisconfigured) {
                self::$warnedMisconfigured = true;
                logger()->warning('PAYMENT_MODE is "stripe" but STRIPE_SECRET is missing or not a test key — falling back to demo payment.');
            }

            return false;
        }

        return true;
    }

    /**
     * Map an order's stored payment_method label to Stripe payment_method_types.
     * Labels come from CheckoutPage's server-side whitelist, so prefix/equality
     * matching is reliable: plain 'FPX' (Stripe-mode orders — the bank is chosen
     * on Stripe's page) and 'FPX - <bank>' (demo-mode orders) both map to fpx.
     * null = not supported by Stripe MY (Touch 'n Go, ShopeePay, Boost, legacy
     * labels) → the order stays on the demo flow.
     */
    public static function paymentMethodTypesFor(?string $label): ?array
    {
        return match (true) {
            $label !== null && str_starts_with($label, 'FPX') => ['fpx'],
            // Stripe's type identifier is 'grabpay' — NO underscore. 'grab_pay'
            // is rejected outright by the API ("Invalid payment_method_types"),
            // which surfaced as "could not reach the payment provider".
            $label === 'GrabPay' => ['grabpay'],
            $label === 'Credit / Debit Card' => ['card'],
            default => null,
        };
    }

    /**
     * Create (or reuse) the hosted Checkout session for an order and stretch the
     * order's payment window to outlive it. Reusing a still-open session stops a
     * two-tab customer from ending up with two live sessions for one order —
     * the settle step is idempotent either way, but a second session could take
     * a second charge that would then need a manual refund.
     *
     * @throws ApiErrorException
     */
    public function createSession(Order $order): Session
    {
        $unitAmount = (int) round(((float) $order->total_amount) * 100);

        if ($order->stripe_session_id) {
            try {
                $existing = $this->retrieveSession($order->stripe_session_id);

                if ($existing->status === 'open' && $existing->expires_at > time()) {
                    if ((int) $existing->amount_total === $unitAmount) {
                        return $existing;
                    }

                    // The order total changed since this session was created
                    // (admin edit while pending): kill the stale session so the
                    // old amount can never be paid, then mint a correct one.
                    try {
                        $this->expireSession($existing->id);
                    } catch (ApiErrorException $e) {
                        logger()->warning('Stripe stale-session expire failed for '.$order->order_number.': '.$e->getMessage());
                    }
                }
            } catch (ApiErrorException $e) {
                logger()->warning('Stripe session reuse lookup failed, creating fresh: '.$e->getMessage());
            }
        }

        $session = $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => self::paymentMethodTypesFor($order->payment_method),
            'line_items' => [[
                'quantity' => 1,
                // One consolidated line for the order total (items + shipping):
                // per-item lines would re-do the rounding the checkout already
                // settled and risk drifting a sen from total_amount.
                'price_data' => [
                    'currency' => 'myr',
                    'unit_amount' => $unitAmount,
                    'product_data' => ['name' => 'Order '.$order->order_number],
                ],
            ]],
            'customer_email' => $order->customer_email,
            'client_reference_id' => $order->order_number,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
            'success_url' => route('payment', $order->order_number).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment', $order->order_number),
            'expires_at' => now()->addMinutes(self::SESSION_MINUTES)->timestamp,
        ]);

        // Eloquent update (not a raw builder) so activitylog records the window
        // extension alongside the session id.
        $order->update([
            'stripe_session_id' => $session->id,
            'expires_at' => Carbon::createFromTimestamp($session->expires_at)
                ->addMinutes(self::EXPIRY_GRACE_MINUTES),
        ]);

        return $session;
    }

    /**
     * @throws ApiErrorException
     */
    public function retrieveSession(string $sessionId): Session
    {
        return $this->client()->checkout->sessions->retrieve($sessionId);
    }

    /**
     * Invalidate an open session so it can never be paid (throws if the
     * session is not open — completed/expired sessions can't be expired).
     *
     * @throws ApiErrorException
     */
    public function expireSession(string $sessionId): void
    {
        $this->client()->checkout->sessions->expire($sessionId);
    }

    /**
     * Best-effort session void for the paths that take an order out of play
     * outside the payment page — admin cancel, customer cancel, shop-close,
     * and manual "Mark Paid". Without it, a customer still sitting on Stripe's
     * hosted checkout could pay an order that was just cancelled (or already
     * settled by hand), forcing a manual refund. Deliberately quiet: a
     * completed/expired session can't be expired (that's fine — the webhook's
     * duplicate/cancelled alerts cover money that has already moved), and a
     * Stripe hiccup must never block the cancellation itself. Gated on the
     * key, not PAYMENT_MODE, so sessions minted before an admin flipped the
     * mode back to demo still get voided.
     */
    public function expireSessionQuietly(?string $sessionId): void
    {
        if (blank($sessionId) || ! str_starts_with((string) config('services.stripe.secret'), 'sk_test_')) {
            return;
        }

        try {
            $this->expireSession($sessionId);
        } catch (ApiErrorException $e) {
            logger()->info("Stripe session void skipped for {$sessionId}: ".$e->getMessage());
        }
    }

    private function client(): StripeClient
    {
        return $this->client ??= new StripeClient(config('services.stripe.secret'));
    }
}
