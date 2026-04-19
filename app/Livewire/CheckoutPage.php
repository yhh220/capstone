<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CheckoutPage extends Component
{
    use SetsSeo;

    public int $step = 1;

    // Step 1: Customer Info
    public string $customerName  = '';
    public string $customerEmail = '';
    public string $customerPhone = '';
    public string $street        = '';
    public string $city          = '';
    public string $postcode      = '';
    public string $state         = 'Selangor';
    public string $orderNotes    = '';

    // Step 2: Payment method
    public string $paymentMethod = 'online_banking';

    // Step 3: Confirmation
    public ?Order $order = null;

    protected $rules = [
        'customerName'  => 'required|string|max:255',
        'customerEmail' => 'required|email|max:255',
        'customerPhone' => 'required|string|max:20',
        'street'        => 'required|string|max:500',
        'city'          => 'required|string|max:255',
        'postcode'      => 'required|string|max:10',
        'state'         => 'required|string|max:100',
    ];

    public function mount(): void
    {
        // Force login — redirect if not authenticated
        if (!Auth::check()) {
            session()->put('url.intended', route('checkout'));
            $this->redirect(route('login'));
            return;
        }

        // Pre-fill from user profile
        $user = Auth::user();
        $this->customerName  = $user->name ?? '';
        $this->customerEmail = $user->email ?? '';

        // Redirect to cart if cart is empty
        $cartItems = CartItem::where('session_id', session()->getId())->count();
        if ($cartItems === 0) {
            $this->redirect(route('cart'));
            return;
        }

        $this->setSeo(title: 'Checkout', description: 'Complete your purchase.');
    }

    public function getCartItemsProperty()
    {
        return CartItem::where('session_id', session()->getId())
            ->with('product')
            ->get();
    }

    public function getSubtotalProperty(): float
    {
        return $this->cartItems->sum(fn($item) =>
            ($item->product->current_price ?? 0) * $item->quantity
        );
    }

    public function goToStep2(): void
    {
        $this->validate();
        $this->step = 2;
    }

    public function goBack(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function placeOrder(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            $this->redirect(route('cart'));
            return;
        }

        $subtotal = $this->subtotal;

        // Create the order
        $this->order = Order::create([
            'user_id'          => Auth::id(),
            'order_number'     => Order::generateOrderNumber(),
            'tracking_number'  => Order::generateTrackingNumber(),
            'customer_name'    => $this->customerName,
            'customer_email'   => $this->customerEmail,
            'customer_phone'   => $this->customerPhone,
            'shipping_address' => [
                'street'   => $this->street,
                'city'     => $this->city,
                'postcode' => $this->postcode,
                'state'    => $this->state,
            ],
            'subtotal'       => $subtotal,
            'total_amount'   => $subtotal,
            'status'         => 'pending',
            'payment_status' => 'paid', // Mock = always paid
        ]);

        // Create order items (snapshot of product at purchase time)
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id'     => $this->order->id,
                'product_id'   => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'quantity'     => $cartItem->quantity,
                'unit_price'   => $cartItem->product->current_price ?? 0,
                'subtotal'     => ($cartItem->product->current_price ?? 0) * $cartItem->quantity,
            ]);

            // Decrease stock
            if ($cartItem->product->stock > 0) {
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }
        }

        // Clear cart
        CartItem::where('session_id', session()->getId())->delete();

        // Send confirmation email
        try {
            $this->order->load('items');
            Mail::to($this->customerEmail)->send(new OrderConfirmationMail($this->order));
        } catch (\Exception $e) {
            // Don't block order on email failure — log it
            logger()->error('Order email failed: ' . $e->getMessage());
        }

        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
