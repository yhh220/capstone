<?php

namespace App\Http\Controllers;

use App\Mail\OwnerAlertMail;
use App\Models\Order;
use App\Services\Payments\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

/**
 * Authoritative confirmation for Stripe Checkout payments. The success-URL
 * return in PaymentPage covers the local/no-webhook case; this endpoint is what
 * production relies on (a customer may close the Stripe tab before redirecting).
 * Signature-verified, CSRF-exempt (bootstrap/app.php), idempotent via the
 * shared OrderPaymentService settle step.
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, OrderPaymentService $payments): Response
    {
        if (blank(config('services.stripe.webhook_secret'))) {
            // Loud and specific: without this, a missing STRIPE_WEBHOOK_SECRET
            // just looks like endless signature failures until Stripe silently
            // disables the endpoint after days of retries.
            logger()->error('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured — rejecting.');
            abort(400, 'Webhook secret not configured');
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                (string) config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException|\UnexpectedValueException $e) {
            logger()->warning('Stripe webhook rejected: '.$e->getMessage());
            abort(400, 'Invalid payload');
        }

        // Unhandled event types are acknowledged immediately — a non-2xx would
        // make Stripe retry them for days.
        $handled = [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded',
            'checkout.session.async_payment_failed',
        ];
        if (! in_array($event->type, $handled, true)) {
            return response('Ignored', 200);
        }

        $session = $event->data->object;
        $order = $this->resolveOrder($session);

        if (! $order) {
            logger()->warning("Stripe webhook {$event->type}: no matching order for session {$session->id}");

            return response('No matching order', 200);
        }

        if ($event->type === 'checkout.session.async_payment_failed') {
            logger()->warning("Stripe async payment failed for order {$order->order_number} (session {$session->id})");
            $this->alertOwner($order, $session, 'Stripe payment failed', 'The customer\'s bank/wallet payment did not complete. The order stays pending and will auto-expire as usual.');

            return response('OK', 200);
        }

        // checkout.session.completed fires for FPX/GrabPay while the payment is
        // still settling (payment_status "unpaid") — the async_payment_succeeded
        // event is the real confirmation, so wait for it.
        if ($session->payment_status !== 'paid') {
            return response('Awaiting async payment', 200);
        }

        $result = $payments->markPaid(
            $order,
            source: 'stripe_webhook',
            paymentIntentId: is_string($session->payment_intent) ? $session->payment_intent : null,
            allowExpired: true, // money has moved — settle even past the window
        );

        if ($result === 'cancelled') {
            // Expiry race or shop-close cancel: the money moved but the stock is
            // already released. Never un-cancel (stock may be resold) — flag for
            // a manual refund in the Stripe test dashboard instead.
            logger()->warning("Stripe payment received for cancelled order {$order->order_number} (session {$session->id}) — manual refund needed");
            $this->alertOwner($order, $session, 'Payment received for a cancelled order — manual refund needed', 'The order was already cancelled (expired or shop closed) when Stripe confirmed payment. Refund it from the Stripe dashboard.');
        }

        if ($result === 'already_paid') {
            // The order settled through some other payment. If THIS event's
            // payment intent isn't the one recorded on the order, real money
            // arrived twice (second session from a race, or a demo/admin settle
            // beating a Stripe payment) — without this alert a double charge
            // would only ever be found by scrolling the Stripe dashboard.
            $intent = is_string($session->payment_intent) ? $session->payment_intent : null;
            $order->refresh();

            if ($intent !== null && $intent !== $order->stripe_payment_intent_id) {
                logger()->warning("Duplicate Stripe payment for order {$order->order_number}: intent {$intent} vs recorded ".($order->stripe_payment_intent_id ?? 'none'));
                $this->alertOwner($order, $session, 'Duplicate payment received — refund needed', 'This order was already paid, but Stripe confirmed a second, different payment for it. Refund the duplicate charge from the Stripe dashboard.');
            }
        }

        return response('OK', 200);
    }

    /**
     * metadata.order_id is the primary key reference we set at session creation;
     * cross-check against client_reference_id (the order number) so a forged or
     * mismatched payload can't settle the wrong order. Falls back to the stored
     * session id for events whose metadata went missing.
     */
    private function resolveOrder(object $session): ?Order
    {
        $order = Order::find((int) ($session->metadata->order_id ?? 0));

        if ($order && $order->order_number === ($session->client_reference_id ?? null)) {
            return $order;
        }

        return Order::where('stripe_session_id', $session->id)->first();
    }

    private function alertOwner(Order $order, object $session, string $heading, string $note): void
    {
        $ownerEmail = OwnerAlertMail::recipient();
        if (! $ownerEmail) {
            return;
        }

        try {
            Mail::to($ownerEmail)->send(new OwnerAlertMail(
                $heading,
                [
                    'Order' => $order->order_number,
                    'Customer' => $order->customer_name,
                    'Total' => 'RM '.number_format((float) $order->total_amount, 2),
                    'Stripe session' => $session->id,
                    'Note' => $note,
                ],
                url('/admin/orders/'.$order->getKey().'/edit'),
                'View order',
            ));
        } catch (\Throwable $e) {
            logger()->error("Stripe webhook owner alert failed for {$order->order_number}: ".$e->getMessage());
        }
    }
}
