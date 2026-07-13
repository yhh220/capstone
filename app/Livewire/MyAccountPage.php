<?php

namespace App\Livewire;

use App\Livewire\Concerns\NotifiesOwner;
use App\Livewire\Concerns\SetsSeo;
use App\Mail\BookingCancelledMail;
use App\Mail\OrderCancelledMail;
use App\Models\Booking;
use App\Models\Order;
use App\Services\RefundCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class MyAccountPage extends Component
{
    use NotifiesOwner, SetsSeo, WithPagination;

    /** 'orders' | 'bookings' */
    public string $tab = 'orders';

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'), navigate: false);
            return;
        }

        // In showroom mode (online shopping off) orders aren't used, so land on
        // bookings instead.
        if (setting('ONLINE_SHOPPING_ENABLED') !== 'true') {
            $this->tab = 'bookings';
        }

        $this->setSeo(
            title: 'My Account',
            description: 'View your orders and service bookings.',
        );
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['orders', 'bookings'], true) ? $tab : 'orders';
        $this->resetPage();
    }

    /**
     * Cancel one of the signed-in user's own bookings. Scoped to user_id so a
     * user can never touch someone else's booking, and only pending/confirmed
     * bookings can be cancelled (mirrors the public BookingTracker guard).
     */
    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::where('user_id', Auth::id())->find($bookingId);

        if (! $booking) {
            return;
        }

        if (in_array($booking->status, ['cancelled', 'completed'], true)) {
            session()->flash('booking_success', __('This booking has already been cancelled or completed.'));
            return;
        }

        $booking->update(['status' => 'cancelled']);
        session()->flash('booking_success', __('Your booking has been cancelled.'));

        // Notify the customer
        try {
            if ($booking->customer_email) {
                Mail::to($booking->customer_email)->send(new BookingCancelledMail($booking->fresh('service')));
            }
        } catch (\Throwable $e) {
            logger()->error('BookingCancelledMail failed: ' . $e->getMessage());
        }

        // Notify the shop owner
        $this->notifyOwner(
            'Booking cancelled by customer',
            [
                'Reference' => $booking->reference,
                'Customer'  => $booking->customer_name,
                'Phone'     => $booking->customer_phone,
                'When'      => $booking->start_at?->format('D, d M Y · g:i A'),
            ],
            url('/admin/bookings/' . $booking->getKey() . '/edit'),
            'View booking',
        );
    }

    /**
     * Cancel one of the signed-in user's own orders. Ownership is enforced in the
     * locked query itself (not a separate check after fetching). Two paths:
     * unpaid orders cancel instantly with no refund math (nothing was charged);
     * paid orders run the same RefundCalculator the admin-side cancel action uses,
     * so the recorded refund never depends on who triggered the cancellation.
     */
    public function cancelOrder(int $orderId): void
    {
        $calculator = new RefundCalculator();

        $order = DB::transaction(function () use ($orderId, $calculator) {
            $order = Order::where('id', $orderId)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->with('items')
                ->first();

            if (! $order) {
                return null;
            }

            if ($order->payment_status === 'pending' && $order->isAwaitingPayment()) {
                $order->restockItems();
                $order->update([
                    'status'              => 'cancelled',
                    'cancelled_by'        => 'customer',
                    'cancellation_reason' => 'Cancelled by customer before payment',
                ]);

                return $order;
            }

            if ($order->payment_status === 'paid' && $calculator->isCancellable($order)) {
                $refund = $calculator->calculate($order);

                if ($refund === null) {
                    return null;
                }

                $order->restockItems();
                $order->update([
                    'status'              => 'cancelled',
                    'cancelled_by'        => 'customer',
                    'cancellation_reason' => 'Cancelled by customer via My Account',
                    'refund_amount'       => $refund['amount'],
                    'refund_percentage'   => $refund['percentage'],
                ]);

                return $order;
            }

            return null;
        });

        if ($order === null) {
            session()->flash('order_cancel_error', __('This order can no longer be cancelled.'));

            return;
        }

        // Void any open Stripe session for the cancelled order — a still-open
        // checkout tab must not be able to pay for stock that was just released.
        app(\App\Services\Payments\StripeCheckoutService::class)
            ->expireSessionQuietly($order->stripe_session_id);

        $order = $order->fresh('items');

        try {
            Mail::to($order->customer_email)->send(new OrderCancelledMail($order));
        } catch (\Throwable $e) {
            logger()->error('Order cancellation email failed: ' . $e->getMessage());
        }

        $this->notifyOwner(
            'Order cancelled',
            [
                'Order Number' => $order->order_number,
                'Customer'     => $order->customer_name,
                'Cancelled By' => 'Customer (My Account)',
                'Reason'       => $order->cancellation_reason,
                'Refund'       => $order->refund_amount !== null
                    ? 'RM ' . number_format($order->refund_amount, 2) . ' (' . $order->refund_percentage . '%)'
                    : null,
            ],
            url('/admin/orders/' . $order->getKey() . '/edit'),
        );

        session()->flash(
            'order_cancel_success',
            $order->refund_amount !== null
                ? __('Order cancelled. A refund of RM :amount (:pct%) has been recorded.', [
                    'amount' => number_format($order->refund_amount, 2),
                    'pct'    => number_format((float) $order->refund_percentage, 0),
                ])
                : __('Order cancelled.')
        );
    }

    public function render()
    {
        $shoppingEnabled = setting('ONLINE_SHOPPING_ENABLED') === 'true';

        return view('livewire.my-account-page', [
            'shoppingEnabled' => $shoppingEnabled,
            'orders' => $this->tab === 'orders'
                ? Order::where('user_id', Auth::id())->with('items.product')->latest()->paginate(10)
                : null,
            'bookings' => $this->tab === 'bookings'
                ? Booking::where('user_id', Auth::id())->with('service')->latest()->paginate(10)
                : null,
        ])->layout('layouts.app');
    }
}
