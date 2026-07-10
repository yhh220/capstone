<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesOwner;
use App\Livewire\Concerns\SetsSeo;
use App\Mail\OrderCancelledMail;
use App\Models\Order;
use App\Services\Payments\OrderPaymentService;
use App\Services\Payments\StripeCheckoutService;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Stripe\Exception\ApiErrorException;

class PaymentPage extends Component
{
    use NotifiesOwner, SetsSeo;

    #[Locked]
    public Order $order;

    /**
     * True while Stripe reports the session's payment still settling (FPX can
     * confirm seconds after the success redirect). Display-only flag.
     */
    public bool $paymentProcessing = false;

    /**
     * Labels the on-page method switcher may set. Bare 'FPX' (no bank suffix)
     * because the switcher only exists in Stripe mode, where the bank is
     * chosen on Stripe's hosted page.
     */
    public const SWITCHABLE_METHODS = [
        'FPX', 'GrabPay', 'Credit / Debit Card',
        "Touch 'n Go eWallet", 'ShopeePay', 'Boost',
    ];

    public function mount(string $orderNumber): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'), navigate: false);

            return;
        }

        // Only the order's owner may view its payment page.
        $found = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items')
            ->first();

        abort_unless($found, 404);

        $this->order = $found;

        // Stripe success-URL return: verify the session server-side and settle
        // if paid. This is what makes local demos work without webhook
        // forwarding — the webhook remains the authoritative path in production.
        // hash_equals against the STORED session id stops a customer from
        // feeding someone else's (or a fabricated) session id into the lookup.
        $sessionId = (string) request()->query('session_id', '');
        if ($sessionId !== ''
            && $this->order->isAwaitingPayment()
            && hash_equals((string) $this->order->stripe_session_id, $sessionId)) {
            $this->settleFromStripeReturn($sessionId);
        }

        // Cancel + restock immediately if the window already lapsed.
        if ($this->order->isPaymentExpired()) {
            $this->expireOrder();
        }

        $this->setSeo(
            title: 'Complete Payment',
            description: 'Securely complete the payment for your order.',
        );
    }

    /**
     * Pay button. Stripe-eligible orders (PAYMENT_MODE=stripe + a card/FPX/
     * GrabPay method) are redirected to a hosted Stripe Checkout session in
     * test mode; everything else keeps the demo flip (no real money moves).
     * Both paths settle through OrderPaymentService, so the paid transition
     * and its confirmation email stay idempotent.
     */
    public function pay(): void
    {
        $stripe = app(StripeCheckoutService::class);

        if ($stripe->enabled() && StripeCheckoutService::paymentMethodTypesFor($this->order->payment_method) !== null) {
            if ($this->order->isPaymentExpired()) {
                $this->expireOrder();

                return;
            }

            // One live session per order: without this lock two concurrent
            // clicks (double-tap, two tabs) could each mint a payable session
            // and the customer could be charged twice.
            $lock = Cache::lock('stripe-session:'.$this->order->id, 15);
            if (! $lock->get()) {
                return;
            }

            try {
                $this->order->refresh();

                // Re-check the order is still payable under the lock: an admin
                // cancel, shop-close, or the expiry job may have cancelled it
                // since this page rendered (a stale tab's Pay click). Without
                // this, a brand-new payable session would be minted for a
                // cancelled, already-restocked order. Mirrors the demo path,
                // where markPaid() re-checks awaiting-payment under its lock.
                if (! $this->order->isAwaitingPayment()) {
                    return;
                }

                // A session this order already holds may have been paid (webhook
                // lag, a second tab) or be mid-settlement (FPX) — never mint a
                // new charge in either case.
                if ($this->order->stripe_session_id && $this->checkExistingSession()) {
                    return;
                }

                $session = $stripe->createSession($this->order);
            } catch (ApiErrorException $e) {
                // Runtime API failure ≠ misconfiguration: don't silently fall
                // back to a demo flip (the customer believes they paid) — show
                // an error and let them retry instead.
                logger()->error('Stripe session creation failed for '.$this->order->order_number.': '.$e->getMessage());
                session()->flash('payment_error', __('Could not reach the payment provider. Please try again.'));

                return;
            } finally {
                $lock->release();
            }

            Breadcrumbs::push('payment', 'Redirecting to Stripe Checkout', ['order' => $this->order->order_number]);
            $this->order->refresh(); // createSession stretched expires_at + stored the session id
            $this->redirect($session->url); // server-driven redirect — CSP form-action 'self' stays intact

            return;
        }

        $result = app(OrderPaymentService::class)->markPaid($this->order, 'demo', causer: Auth::user());

        if ($result === 'expired') {
            $this->expireOrder();

            return;
        }

        $this->order->refresh();

        if ($result !== 'paid') {
            return; // a concurrent request already settled this order
        }

        session()->flash('payment_success', __('Payment successful! Your order is confirmed.'));

        // The awaiting-payment view (countdown + items + notice + button) is much
        // taller than the success card it's replaced by; without this the page
        // stays at its old scroll offset and the success card ends up off-screen
        // above the fold, looking like it landed in the footer.
        $this->dispatch('scroll-top');
    }

    /**
     * Switch a pending order's payment method from the payment page — the
     * escape hatch for a method that fails on Stripe's side (e.g. a wallet the
     * account has not activated). Stripe mode only: in demo mode the method is
     * cosmetic and the demo flip succeeds regardless.
     */
    public function changePaymentMethod(string $method): void
    {
        $stripe = app(StripeCheckoutService::class);

        if (! $stripe->enabled() || ! in_array($method, self::SWITCHABLE_METHODS, true)) {
            return;
        }

        // Same lock as pay(): a method change must not race session creation.
        $lock = Cache::lock('stripe-session:'.$this->order->id, 15);
        if (! $lock->get()) {
            return;
        }

        try {
            $this->order->refresh();

            if (! $this->order->isAwaitingPayment()
                || $this->order->isPaymentExpired()
                || $method === $this->order->payment_method) {
                return;
            }

            // Kill any live session for the old method so it can't be paid
            // later alongside a new one. But NEVER switch away from a session
            // the customer already completed: its money may have arrived
            // (settle it) or its async FPX/GrabPay payment may still be
            // settling (show the processing notice) — in both cases switching
            // methods now would invite a second charge on the new method.
            // checkExistingSession() does exactly that triage.
            if ($this->order->stripe_session_id) {
                if ($this->checkExistingSession()) {
                    return;
                }

                try {
                    $stripe->expireSession($this->order->stripe_session_id);
                } catch (ApiErrorException $e) {
                    logger()->warning('Stripe session expire on method change failed for '.$this->order->order_number.': '.$e->getMessage());
                }
            }

            // Eloquent update so activitylog records the method change.
            $this->order->update([
                'payment_method' => $method,
                'stripe_session_id' => null,
            ]);
            $this->order->refresh();
        } finally {
            $lock->release();
        }
    }

    /**
     * Pre-flight for pay(): inspect the order's existing Stripe session and
     * return true when no new session may be created — either the money has
     * already arrived (settle + success) or an async payment is still
     * settling (show the processing notice). Returns false when the session
     * is stale/open and pay() should proceed to createSession(), which
     * itself reuses a still-open session.
     */
    private function checkExistingSession(): bool
    {
        try {
            $session = app(StripeCheckoutService::class)->retrieveSession($this->order->stripe_session_id);
        } catch (ApiErrorException $e) {
            // Unreadable stored session (deleted test data, corrupt id): fall
            // through to createSession rather than dead-ending the payment.
            logger()->warning('Stripe pre-pay session check failed for '.$this->order->order_number.': '.$e->getMessage());

            return false;
        }

        if ($session->payment_status === 'paid') {
            app(OrderPaymentService::class)->markPaid(
                $this->order,
                source: 'stripe_return',
                paymentIntentId: is_string($session->payment_intent) ? $session->payment_intent : null,
                causer: Auth::user(),
                allowExpired: true,
            );
            $this->order->refresh();
            session()->flash('payment_success', __('Payment successful! Your order is confirmed.'));
            $this->dispatch('scroll-top');

            return true;
        }

        if ($session->status === 'complete') {
            // Completed checkout, money not confirmed yet (FPX settling): a new
            // session here would invite a second charge — wait for the webhook
            // or the poll below to finish the first one.
            $this->paymentProcessing = true;

            return true;
        }

        return false;
    }

    /**
     * wire:poll while the processing notice shows — re-verifies the session so
     * an FPX payment settles on-page even without webhook delivery, instead of
     * requiring a manual refresh.
     */
    public function pollPaymentStatus(): void
    {
        if ($this->paymentProcessing && $this->order->isAwaitingPayment() && $this->order->stripe_session_id) {
            $this->settleFromStripeReturn($this->order->stripe_session_id);
        }
    }

    /** Settle after returning from Stripe with ?session_id= (see mount()). */
    private function settleFromStripeReturn(string $sessionId): void
    {
        try {
            $session = app(StripeCheckoutService::class)->retrieveSession($sessionId);
        } catch (\Throwable $e) {
            logger()->error('Stripe return verification failed for '.$this->order->order_number.': '.$e->getMessage());

            return; // leave the order pending — the webhook will settle it
        }

        if ($session->payment_status !== 'paid') {
            // FPX/GrabPay can redirect back before the payment finishes settling.
            $this->paymentProcessing = true;

            return;
        }

        $result = app(OrderPaymentService::class)->markPaid(
            $this->order,
            source: 'stripe_return',
            paymentIntentId: is_string($session->payment_intent) ? $session->payment_intent : null,
            causer: Auth::user(),
            allowExpired: true, // money has moved — settle even past the window
        );

        $this->order->refresh();

        if ($result === 'paid') {
            session()->flash('payment_success', __('Payment successful! Your order is confirmed.'));
        }
    }

    /**
     * Cancel an unpaid, timed-out order and release its reserved stock. Locked +
     * guarded so the scheduler and the client timer can't double-restock.
     */
    public function expireOrder(): void
    {
        $expired = DB::transaction(function () {
            $order = Order::where('id', $this->order->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()->with('items')->first();

            // isPaymentExpired() too, not just awaiting-payment: expireOrder() is a
            // public Livewire action, so without the time check a customer could
            // invoke it early (browser console) and stamp their own cancellation
            // as cancelled_by='system' / "payment not completed", polluting the
            // audit trail. The on-page timer is server-seeded, so a legitimate
            // call always arrives with expires_at already past; if a fast client
            // clock fires a second early, the re-render re-seeds the timer and
            // the next tick lands after expiry — it self-heals.
            if (! $order || ! $order->isAwaitingPayment() || ! $order->isPaymentExpired()) {
                return false;
            }

            $order->restockItems();
            $order->update([
                'status' => 'cancelled',
                'cancelled_by' => 'system',
                'cancellation_reason' => 'Order expired — payment not completed in time',
            ]);

            return $order;
        });

        $this->order->refresh();

        if ($expired) {
            $this->dispatch('scroll-top');

            try {
                if ($expired->customer_email) {
                    Mail::to($expired->customer_email)->send(new OrderCancelledMail($expired));
                }
            } catch (\Throwable $e) {
                logger()->error('Order expiry email failed: '.$e->getMessage());
            }

            $this->notifyOwner(
                'Order auto-expired (not paid)',
                [
                    'Order' => $expired->order_number,
                    'Customer' => $expired->customer_name,
                    'Total' => 'RM '.number_format($expired->total_amount, 2),
                    'Reason' => 'Payment timer ran out',
                ],
                url('/admin/orders/'.$expired->getKey().'/edit'),
                'View order',
            );
        }
    }

    public function render()
    {
        $stripe = app(StripeCheckoutService::class);
        $stripeEnabled = $stripe->enabled();

        return view('livewire.payment-page', [
            // Drives the pay-button copy + notice: Stripe test-mode vs demo.
            'isStripeCheckout' => $stripeEnabled
                && StripeCheckoutService::paymentMethodTypesFor($this->order->payment_method) !== null,
            // The method switcher shows for ANY pending order in Stripe mode —
            // including one currently on a demo wallet, so it can switch back.
            'stripeEnabled' => $stripeEnabled,
            'methodOptions' => [
                'FPX' => __('FPX Online Banking'),
                'GrabPay' => 'GrabPay',
                'Credit / Debit Card' => __('Credit / Debit Card'),
                "Touch 'n Go eWallet" => "Touch 'n Go eWallet",
                'ShopeePay' => 'ShopeePay',
                'Boost' => 'Boost',
            ],
        ])->layout('layouts.app');
    }
}
