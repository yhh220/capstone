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

    // Step 2: Payment method (display-only demo — no real gateway)
    public string $paymentMethod = 'fpx';

    public string $fpxBank = 'Maybank2u';

    public string $ewallet = "Touch 'n Go eWallet";

    /** FPX participating banks shown in the demo bank picker. */
    public const FPX_BANKS = [
        'Maybank2u', 'CIMB Clicks', 'Public Bank PBe', 'RHB Now', 'Hong Leong Connect',
        'AmOnline', 'Bank Islam', 'Bank Rakyat', 'Affin Bank', 'Alliance Bank',
        'BSN', 'OCBC', 'HSBC', 'Standard Chartered', 'UOB',
    ];

    /** E-wallet providers shown in the demo wallet picker. */
    public const EWALLETS = [
        "Touch 'n Go eWallet", 'GrabPay', 'ShopeePay', 'Boost',
    ];

    // Step 3: Confirmation
    public ?Order $order = null;

    protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'required|email|max:255',
        'customerPhone' => 'required|string|regex:/^[0-9]{8,15}$/',
        'street' => 'required|string|max:500',
        'city' => 'required|string|max:255',
        'postcode' => 'required|digits:5',
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

    public function getShippingProperty(): float
    {
        return app(\App\Services\ShippingCalculator::class)->fee($this->subtotal);
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal + $this->shipping;
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
        if (! in_array($this->paymentMethod, ['fpx', 'ewallet', 'card', 'cod'], true)) {
            $this->paymentMethod = 'fpx';
        }
        if (! in_array($this->fpxBank, self::FPX_BANKS, true)) {
            $this->fpxBank = self::FPX_BANKS[0];
        }
        if (! in_array($this->ewallet, self::EWALLETS, true)) {
            $this->ewallet = self::EWALLETS[0];
        }

        // Human-readable provider label stored on the order (e.g. "FPX - Maybank2u").
        $paymentLabel = match ($this->paymentMethod) {
            'fpx' => 'FPX - ' . $this->fpxBank,
            'ewallet' => $this->ewallet,
            'card' => 'Credit / Debit Card',
            'cod' => 'Cash on Delivery',
            default => 'FPX',
        };

        // COD is confirmed on placement (paid in person on delivery) — no online
        // payment page, no 15-minute auto-cancel timer.
        $isCod = $this->paymentMethod === 'cod';

        try {
            $order = DB::transaction(function () use ($paymentLabel, $isCod) {
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
                $shippingFee = app(\App\Services\ShippingCalculator::class)->fee($subtotal);

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => Order::generateOrderNumber(),
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
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $subtotal + $shippingFee,
                    'status' => $isCod ? 'processing' : 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $paymentLabel,
                    'notes' => $this->orderNotes ?: null,
                    // Online orders get a 15-minute window to pay before auto-cancel;
                    // COD has no timer (settled on delivery).
                    'expires_at' => $isCod ? null : now()->addMinutes(15),
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

        // COD: order is confirmed now (cash collected on delivery) — send the
        // confirmation email at placement and skip the online payment page.
        if ($isCod) {
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order->fresh('items')));
            } catch (\Throwable $e) {
                logger()->error('COD order confirmation email failed: ' . $e->getMessage());
            }

            session()->flash('success', __('Order placed! Please have cash ready when your order is delivered.'));
            $this->redirect(route('account'), navigate: false);

            return;
        }

        // Online methods → demo payment page. The order stays "pending payment"
        // until the customer pays (or the 15-minute timer expires); the
        // confirmation email is sent once payment succeeds, not at placement.
        $this->redirect(route('payment', $order->order_number), navigate: false);
    }

    public function render()
    {
        return view('livewire.checkout-page', [
            'fpxBanks' => self::FPX_BANKS,
            'ewallets' => self::EWALLETS,
        ])->layout('layouts.app');
    }
}
