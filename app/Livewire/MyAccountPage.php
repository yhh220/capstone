<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Booking;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyAccountPage extends Component
{
    use SetsSeo, WithPagination;

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

        if (! in_array($booking->status, ['cancelled', 'completed'], true)) {
            $booking->update(['status' => 'cancelled']);
            session()->flash('booking_success', __('Your booking has been cancelled.'));
        }
    }

    public function render()
    {
        $shoppingEnabled = setting('ONLINE_SHOPPING_ENABLED') === 'true';

        return view('livewire.my-account-page', [
            'shoppingEnabled' => $shoppingEnabled,
            'orders' => $this->tab === 'orders'
                ? Order::where('user_id', Auth::id())->with('items')->latest()->paginate(10)
                : null,
            'bookings' => $this->tab === 'bookings'
                ? Booking::where('user_id', Auth::id())->with('service')->latest()->paginate(10)
                : null,
        ])->layout('layouts.app');
    }
}
