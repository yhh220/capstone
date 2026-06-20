<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Order;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class OrderTracker extends Component
{
    use SetsSeo;

    public string $orderNumber = '';
    public ?Order $order       = null;
    public bool $searched      = false;
    public string $errorMsg    = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Track Your Order',
            description: 'Enter your order number to check the status of your order.',
        );
    }

    public function trackOrder(): void
    {
        $this->validate([
            'orderNumber' => 'required|string',
        ]);

        $this->searched = true;
        $this->errorMsg = '';

        $throttleKey = 'track-order:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->errorMsg = __('Too many tracking attempts. Please try again in :seconds seconds.', ['seconds' => $seconds]);
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $this->order = Order::where('order_number', $this->orderNumber)
            ->with('items')
            ->first();

        if (!$this->order) {
            $this->errorMsg = __('No order found. Please check your order number.');
        } else {
            RateLimiter::clear($throttleKey);
        }
    }

    /** Lucide icon markup (lucide.dev, MIT) keyed by status — keeps the timeline's icons SVG, not emoji. */
    private const ICONS = [
        'pending'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6M9 16h6M9 8h6M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/></svg>',
        'processing' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        'shipped'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>',
        'delivered'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
        'cancelled'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
    ];

    /**
     * Get the status steps for timeline display.
     */
    public function getStatusStepsProperty(): array
    {
        $steps = [
            ['key' => 'pending',    'label' => __('Order Placed'),  'icon' => self::ICONS['pending']],
            ['key' => 'processing', 'label' => __('Processing'),    'icon' => self::ICONS['processing']],
            ['key' => 'shipped',    'label' => __('Shipped'),       'icon' => self::ICONS['shipped']],
            ['key' => 'delivered',  'label' => __('Delivered'),     'icon' => self::ICONS['delivered']],
        ];

        if ($this->order && $this->order->status === 'cancelled') {
            $steps[] = ['key' => 'cancelled', 'label' => __('Cancelled'), 'icon' => self::ICONS['cancelled']];
        }

        return $steps;
    }

    public function render()
    {
        return view('livewire.order-tracker')->layout('layouts.app');
    }
}
