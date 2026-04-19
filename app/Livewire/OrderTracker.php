<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\Order;
use Livewire\Component;

class OrderTracker extends Component
{
    use SetsSeo;

    public string $orderNumber = '';
    public string $email       = '';
    public ?Order $order       = null;
    public bool $searched      = false;
    public string $errorMsg    = '';

    public function mount(): void
    {
        $this->setSeo(
            title: 'Track Your Order',
            description: 'Enter your order number and email to track your order status.',
        );
    }

    public function trackOrder(): void
    {
        $this->validate([
            'orderNumber' => 'required|string',
            'email'       => 'required|email',
        ]);

        $this->searched = true;
        $this->errorMsg = '';

        $this->order = Order::where('order_number', $this->orderNumber)
            ->where('customer_email', $this->email)
            ->with('items')
            ->first();

        if (!$this->order) {
            $this->errorMsg = __('No order found. Please check your order number and email address.');
        }
    }

    /**
     * Get the status steps for timeline display.
     */
    public function getStatusStepsProperty(): array
    {
        $steps = [
            ['key' => 'pending',    'label' => __('Order Placed'),  'icon' => '📋'],
            ['key' => 'processing', 'label' => __('Processing'),    'icon' => '⏳'],
            ['key' => 'shipped',    'label' => __('Shipped'),       'icon' => '📦'],
            ['key' => 'delivered',  'label' => __('Delivered'),     'icon' => '✅'],
        ];

        if ($this->order && $this->order->status === 'cancelled') {
            $steps[] = ['key' => 'cancelled', 'label' => __('Cancelled'), 'icon' => '❌'];
        }

        return $steps;
    }

    public function render()
    {
        return view('livewire.order-tracker')->layout('layouts.app');
    }
}
