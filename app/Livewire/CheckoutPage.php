<?php

namespace App\Livewire;

use App\Livewire\Concerns\SetsSeo;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Payments\StripeCheckoutService;
use App\Services\ShippingCalculator;
use App\Support\Breadcrumbs;
use App\Support\DeliveryArea;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
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

    // Step 2: Payment method. The choice is stored on the order as a label;
    // whether it then pays via Stripe (card/FPX/GrabPay in stripe mode) or the
    // demo flip is decided on the payment page.
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

    /** Max units of any single product per order (backorder sanity cap). */
    public const MAX_QTY_PER_ITEM = 99;

    protected function rules(): array
    {
        return [
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'required|email|max:255',
            'customerPhone' => 'required|string|regex:/^[0-9]{8,15}$/',
            'street' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'postcode' => 'required|digits:5',
            // Whitelisted to deliverable states — East Malaysia is not served,
            // so a crafted request can't order to an address we can't ship to.
            'state' => ['required', 'string', Rule::in(DeliveryArea::STATES)],
            'orderNotes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(): void
    {
        // Force login — redirect if not authenticated
        if (! Auth::check()) {
            session()->put('url.intended', route('checkout'));
            $this->redirect(route('login'));

            return;
        }

        // Social-login accounts must set a password before they can check out.
        if (! Auth::user()->hasPassword()) {
            session()->flash('success', __('Please set a password on your account before checking out.'));
            $this->redirect(route('profile'), navigate: false);

            return;
        }

        // Pre-fill from user profile + last order's delivery address
        $user = Auth::user();
        $this->customerName = $user->name ?? '';
        $this->customerEmail = $user->email ?? '';

        $lastOrder = Order::where('user_id', $user->id)
            ->whereNotNull('shipping_address')
            ->latest()
            ->first();

        if ($lastOrder) {
            $addr = $lastOrder->shipping_address;
            $this->customerPhone = $lastOrder->customer_phone ?? '';
            $this->street = $addr['street'] ?? '';
            $this->city = $addr['city'] ?? '';
            $this->postcode = $addr['postcode'] ?? '';
            $this->state = $addr['state'] ?? '';
        }

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
        return app(ShippingCalculator::class)->fee($this->subtotal);
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
        // Livewire keeps the error bag across requests and addError() appends,
        // while @error shows first() — reset so retries report fresh messages.
        $this->resetErrorBag();

        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        // Re-validate here so that placeOrder() can't be called directly via
        // a Livewire XHR request without going through goToStep2() first.
        $this->validate();

        // Throttle order creation — a scripted account shouldn't be able to
        // flood the orders table with junk.
        $throttleKey = 'checkout:'.Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('stock', __('Too many orders placed. Please try again in :seconds seconds.', ['seconds' => $seconds]));

            return;
        }
        RateLimiter::hit($throttleKey, 3600);

        if (CartItem::forCurrentOwner()->count() === 0) {
            $this->redirect(route('cart'));

            return;
        }

        // Whitelist the payment method — never trust the client-supplied value.
        if (! in_array($this->paymentMethod, ['fpx', 'ewallet', 'card'], true)) {
            $this->paymentMethod = 'fpx';
        }
        if (! in_array($this->fpxBank, self::FPX_BANKS, true)) {
            $this->fpxBank = self::FPX_BANKS[0];
        }
        if (! in_array($this->ewallet, self::EWALLETS, true)) {
            $this->ewallet = self::EWALLETS[0];
        }

        // Human-readable provider label stored on the order (e.g. "FPX - Maybank2u").
        // In Stripe mode no bank suffix is recorded: the customer picks the bank
        // on Stripe's hosted page, so any bank named here would be a guess.
        $stripeCheckout = app(StripeCheckoutService::class)->enabled();
        $paymentLabel = match ($this->paymentMethod) {
            'fpx' => $stripeCheckout ? 'FPX' : 'FPX - '.$this->fpxBank,
            'ewallet' => $this->ewallet,
            'card' => 'Credit / Debit Card',
            default => 'FPX',
        };

        Breadcrumbs::push('checkout', 'Placing order', ['method' => $this->paymentMethod]);

        try {
            $order = DB::transaction(function () use ($paymentLabel) {
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

                // A product that vanished, was deactivated, or is out of stock can't be ordered.
                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);
                    if (! $product || ! $product->is_active) {
                        throw new \RuntimeException(__('A product in your cart is no longer available.'));
                    }
                    if ($cartItem->quantity < 1 || $cartItem->quantity > self::MAX_QTY_PER_ITEM) {
                        throw new \RuntimeException(__('Please order between 1 and :max of each item.', ['max' => self::MAX_QTY_PER_ITEM]));
                    }
                    // No stock gate here — out-of-stock items can be backordered (same
                    // rule the cart and product pages already advertise). stock is left
                    // to go negative below, representing units owed to customers; it
                    // nets back to a normal count whenever the product is restocked.
                }

                $lineItems = $cartItems->map(function ($cartItem) use ($products): array {
                    $product = $products->get($cartItem->product_id);
                    $unitPrice = (float) ($product->current_price ?? 0);

                    return [
                        'cart_item' => $cartItem,
                        'product' => $product,
                        'unit_price' => $unitPrice,
                        // Round each line so float math can't drift before storage.
                        'subtotal' => round($unitPrice * $cartItem->quantity, 2),
                    ];
                });

                $subtotal = round($lineItems->sum('subtotal'), 2);
                $shippingFee = app(ShippingCalculator::class)->fee($subtotal);

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
                    'total_amount' => round($subtotal + $shippingFee, 2),
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $paymentLabel,
                    'notes' => $this->orderNotes ?: null,
                    'expires_at' => now()->addMinutes(15),
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
        } catch (QueryException $e) {
            // Order::generateOrderNumber() can't take a row lock on a number that
            // doesn't exist yet, so two checkouts racing for the year's very first
            // order number could both pass its collision check. The unique index
            // on order_number is the real guard — it rejects the duplicate insert,
            // but its raw SQL message (with column/table names) isn't fit to show
            // a customer, so it's logged and swapped for a friendly retry prompt.
            logger()->error('Checkout order_number collision: '.$e->getMessage());
            $this->addError('stock', __('Something went wrong placing your order. Please try again.'));
            $this->step = 1;

            return;
        } catch (\RuntimeException $e) {
            $this->addError('stock', $e->getMessage());
            $this->step = 1;

            return;
        }

        // Redirect to the payment page; confirmation email is sent once payment succeeds.
        $this->redirect(route('payment', $order->order_number), navigate: false);
    }

    public function render()
    {
        return view('livewire.checkout-page', [
            'fpxBanks' => self::FPX_BANKS,
            'ewallets' => self::EWALLETS,
            // Drives the payment-step notice + button copy (Stripe test mode vs
            // pure demo). Whether a given order actually redirects to Stripe is
            // decided per payment method on the payment page.
            'stripeEnabled' => app(StripeCheckoutService::class)->enabled(),
        ])->layout('layouts.app');
    }
}
