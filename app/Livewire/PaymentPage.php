<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
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
        $this->order->refresh();

        if ($this->order->isPaymentExpired()) {
            $this->expireOrder();
            return;
        }

        if (! $this->order->isAwaitingPayment()) {
            return; // already paid or cancelled
        }

        $this->order->update([
            'payment_status' => 'paid',
            'status'         => 'processing',
            'expires_at'     => null,
        ]);

        try {
            Mail::to($this->order->customer_email)->queue(new OrderConfirmationMail($this->order->fresh('items')));
        } catch (\Throwable $e) {
            logger()->error('Order confirmation email failed: ' . $e->getMessage());
        }

        $this->order->refresh();
        session()->flash('payment_success', __('Payment successful! Your order is confirmed.'));
    }

    /**
     * Cancel an unpaid, timed-out order and release its reserved stock. Locked +
     * guarded so the scheduler and the client timer can't double-restock.
     */
    public function expireOrder(): void
    {
        DB::transaction(function () {
            $order = Order::where('id', $this->order->id)->lockForUpdate()->with('items')->first();

            if (! $order || ! $order->isAwaitingPayment()) {
                return;
            }

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.payment-page')->layout('layouts.app');
    }
}
