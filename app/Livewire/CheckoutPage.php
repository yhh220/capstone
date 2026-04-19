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
        return $this->cartItems->sum(fn($item) =>
            ($item->product?->current_price ?? 0) * $item->quantity
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

        try {
            $order = DB::transaction(function () use ($cartItems, $subtotal) {
                // Lock the product rows for the duration of the transaction so concurrent
                // checkouts cannot double-sell the last unit of stock.
                $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Pre-flight stock check — fail the whole order if anything is short.
                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);
                    if (!$product) {
                        throw new \RuntimeException(__('A product in your cart is no longer available.'));
                    }
                    if (($product->stock ?? 0) < $cartItem->quantity) {
                        throw new \RuntimeException(
                            __('Insufficient stock for ":name" — only :stock left.', ['name' => $product->name, 'stock' => $product->stock])
                        );
                    }
                }

                $order = Order::create([
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
                    'payment_status' => 'paid', // Mock payment — always marked paid.
                ]);

                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $cartItem->product_id,
                        'product_name' => $product->name,
                        'quantity'     => $cartItem->quantity,
                        'unit_price'   => $product->current_price ?? 0,
                        'subtotal'     => ($product->current_price ?? 0) * $cartItem->quantity,
                    ]);

                    $product->decrement('stock', $cartItem->quantity);
                }

                CartItem::forCurrentOwner()->delete();

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
            logger()->error('Order email failed: ' . $e->getMessage());
        }

        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.checkout-page')->layout('layouts.app');
    }
}
