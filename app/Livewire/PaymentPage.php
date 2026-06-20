<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PaymentPage extends Component
{
    use SetsSeo;

    public Order $order;

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
     * Demo payment — marks the order paid (no real money moves) and sends the
     * confirmation email. Guarded against paying an expired/cancelled/paid order.
     */
    public function pay(): void
    {
        // Layer 1 — atomic lock (works on the database cache store; Redis if set).
        // If another in-flight request already holds it, this duplicate bails out.
        $lock = Cache::lock('pay-order:' . $this->order->id, 10);

        if (! $lock->get()) {
            return;
        }

        try {
            // Layer 2 — pessimistic row lock inside a transaction, and Layer 3 —
            // an atomic conditional flip whose affected-row count is the single-
            // winner / "unique-index" bottom guard: only the request that finds the
            // order still 'pending' transitions it, so the email can fire only once.
            $result = DB::transaction(function () {
                $order = Order::where('id', $this->order->id)->lockForUpdate()->first();

                if (! $order || ! $order->isAwaitingPayment()) {
                    return 'noop'; // already paid / cancelled
                }
                if ($order->isPaymentExpired()) {
                    return 'expired';
                }

                $affected = Order::where('id', $order->id)
                    ->where('payment_status', 'pending')
                    ->where('status', '!=', 'cancelled')
                    ->update([
                        'payment_status' => 'paid',
                        'status'         => 'processing',
                        'expires_at'     => null,
                        'paid_at'        => now(),
                    ]);

                return $affected === 1 ? 'paid' : 'noop';
            });
        } finally {
            $lock->release();
        }

        if ($result === 'expired') {
            $this->expireOrder();
            return;
        }

        $this->order->refresh();

        if ($result !== 'paid') {
            return; // a concurrent request already settled this order
        }

        \App\Support\Breadcrumbs::push('payment', 'Order paid', ['order' => $this->order->order_number]);

        try {
            \App\Support\Breadcrumbs::push('mail', 'Sending order confirmation');
            Mail::to($this->order->customer_email)->send(new OrderConfirmationMail($this->order->fresh('items')));
        } catch (\Throwable $e) {
            logger()->error('Order confirmation email failed: ' . $e->getMessage());
        }

        session()->flash('payment_success', __('Payment successful! Your order is confirmed.'));

        // The awaiting-payment view (countdown + items + notice + button) is much
        // taller than the success card it's replaced by; without this the page
        // stays at its old scroll offset and the success card ends up off-screen
        // above the fold, looking like it landed in the footer.
        $this->dispatch('scroll-top');
    }

    /**
     * Cancel an unpaid, timed-out order and release its reserved stock. Locked +
     * guarded so the scheduler and the client timer can't double-restock.
     */
    public function expireOrder(): void
    {
        $expired = DB::transaction(function () {
            $order = Order::where('id', $this->order->id)->lockForUpdate()->with('items')->first();

            if (! $order || ! $order->isAwaitingPayment()) {
                return false;
            }

            $order->restockItems();
            $order->update(['status' => 'cancelled']); // event stamps cancelled_at

            return true;
        });

        $this->order->refresh();

        // Same tall-countdown-view → short-cancelled-card shrink as pay(); keep
        // the result visible instead of leaving the scroll position in the footer.
        if ($expired) {
            $this->dispatch('scroll-top');
        }
    }

    public function render()
    {
        return view('livewire.payment-page')->layout('layouts.app');
    }
}
