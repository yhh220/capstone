<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CheckoutPage extends Component
{
    use SetsSeo;

    public int $step = 1;

    // Step 1: Customer Info
    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    public string $street = '';

    public string $city = '';

    public string $postcode = '';

    public string $state = 'Selangor';

    public string $orderNotes = '';

    // Step 2: Payment method
    public string $paymentMethod = 'online_banking';

    // Step 3: Confirmation
    public ?Order $order = null;

    protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'required|email|max:255',
        'customerPhone' => 'required|string|max:20',
        'street' => 'required|string|max:500',
        'city' => 'required|string|max:255',
        'postcode' => 'required|string|max:10',
        'state' => 'required|string|max:100',
    ];

    public function mount(): void
    {
        // Force login — redirect if not authenticated
        if (! Auth::check()) {
            session()->put('url.intended', route('checkout'));
            $this->redirect(route('login'));

            return;
        }

        // Pre-fill from user profile
        $user = Auth::user();
        $this->customerName = $user->name ?? '';
        $this->customerEmail = $user->email ?? '';

        // Redirect to cart if cart is empty
        if (CartItem::forCurrentOwner()->count() === 0) {
            $this->redirect(route('cart'));

            return;
        }

        $this->setSeo(title: 'Checkout', description: 'Complete your purchase.');
    }

    public function getCartItemsProperty()
    {
        return CartItem::forCurrentOwner()->with('product')->get();
    }

    public function getSubtotalProperty(): float
    {
        return $this->cartItems->sum(fn ($item) => ($item->product?->current_price ?? 0) * $item->quantity
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
        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        // Throttle order creation — a scripted account shouldn't be able to
        // flood the orders table with junk.
        $throttleKey = 'checkout:' . Auth::id();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('stock', __('Too many orders placed. Please try again in :seconds seconds.', ['seconds' => $seconds]));

            return;
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 3600);

        if (CartItem::forCurrentOwner()->count() === 0) {
            $this->redirect(route('cart'));

            return;
        }

        // Whitelist the payment method — never trust the client-supplied value.
        if (! in_array($this->paymentMethod, ['online_banking', 'cod'], true)) {
            $this->paymentMethod = 'online_banking';
        }

        try {
            $order = DB::transaction(function () {
                $cartItems = CartItem::forCurrentOwner()
                    ->lockForUpdate()
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \RuntimeException(__('Your cart is empty. Please review your cart before checking out.'));
                }

                // Lock the product rows for the duration of the transaction so concurrent
                // checkouts cannot double-sell the last unit of stock.
                $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // A product that vanished or was deactivated can't be ordered.
                // Insufficient stock is fine — it's a backorder (stock may go
                // negative, representing units owed).
                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);
                    if (! $product || ! $product->is_active) {
                        throw new \RuntimeException(__('A product in your cart is no longer available.'));
                    }
                }

                $lineItems = $cartItems->map(function ($cartItem) use ($products): array {
                    $product = $products->get($cartItem->product_id);
                    $unitPrice = (float) ($product->current_price ?? 0);

                    return [
                        'cart_item' => $cartItem,
                        'product' => $product,
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice * $cartItem->quantity,
                    ];
                });

                $subtotal = $lineItems->sum('subtotal');

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => Order::generateOrderNumber(),
                    'tracking_number' => Order::generateTrackingNumber(),
                    'customer_name' => $this->customerName,
                    'customer_email' => $this->customerEmail,
                    'customer_phone' => $this->customerPhone,
                    'shipping_address' => [
                        'street' => $this->street,
                        'city' => $this->city,
                        'postcode' => $this->postcode,
                        'state' => $this->state,
                    ],
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'notes' => $this->orderNotes ?: null,
                ]);

                foreach ($lineItems as $lineItem) {
                    $cartItem = $lineItem['cart_item'];
                    $product = $lineItem['product'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'product_name' => $product->name,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $lineItem['unit_price'],
                        'subtotal' => $lineItem['subtotal'],
                    ]);

                    $product->decrement('stock', $cartItem->quantity);
                }

                CartItem::forCurrentOwner()
                    ->whereKey($cartItems->pluck('id'))
                    ->delete();

                return $order;
            });
        } catch (\RuntimeException $e) {
            $this->addError('stock', $e->getMessage());
            $this->step = 1;

            return;
        }

        $this->order = $order;

        try {
            $this->order->load('items');
            Mail::to($this->customerEmail)->queue(new OrderConfirmationMail($this->order));
        } catch (\Exception $e) {
            // Don't block confirmation on email failure.
            logger()->error('Order email failed: '.$e->getMessage());
        }

        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
