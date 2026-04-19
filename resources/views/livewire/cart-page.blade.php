<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Shopping Cart') }}</h1>
            <p class="text-gray-400">{{ __('Review your items before checkout') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        @if($this->cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($this->cartItems as $item)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex gap-4 items-center" wire:key="cart-{{ $item->id }}">
                    {{-- Product Image --}}
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden flex-shrink-0">
                        @if($item->product->image)
                            <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl" aria-hidden="true">🚗</div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('product.show', $item->product->slug) }}" class="font-bold text-gray-800 dark:text-gray-200 hover:text-brand-red transition-colors line-clamp-1">
                            {{ $item->product->name }}
                        </a>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            RM {{ number_format($item->product->current_price, 2) }} {{ __('each') }}
                        </div>

                        {{-- Quantity Controls --}}
                        <div class="flex items-center gap-3 mt-3">
                            <button wire:click="decrementQuantity({{ $item->id }})"
                                    class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-bold"
                                    aria-label="{{ __('Decrease quantity') }}">
                                −
                            </button>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200 w-8 text-center tabular-nums">
                                {{ $item->quantity }}
                            </span>
                            <button wire:click="incrementQuantity({{ $item->id }})"
                                    class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-bold"
                                    aria-label="{{ __('Increase quantity') }}">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Item Subtotal + Remove --}}
                    <div class="text-right flex-shrink-0">
                        <div class="font-black text-brand-red text-lg tabular-nums">
                            RM {{ number_format(($item->product->current_price ?? 0) * $item->quantity, 2) }}
                        </div>
                        <button wire:click="removeItem({{ $item->id }})"
                                wire:confirm="{{ __('Remove this item?') }}"
                                class="text-xs text-gray-400 hover:text-red-500 transition-colors mt-2 underline"
                                aria-label="{{ __('Remove') }} {{ $item->product->name }}">
                            {{ __('Remove') }}
                        </button>
                    </div>
                </div>
                @endforeach

                <div class="text-right">
                    <button wire:click="clearCart" wire:confirm="{{ __('Clear all items from your cart?') }}"
                            class="text-sm text-gray-500 hover:text-red-500 transition-colors underline">
                        {{ __('Clear Cart') }}
                    </button>
                </div>
            </div>

            {{-- Order Summary --}}
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 sticky top-24">
                    <h2 class="text-xl font-black text-gray-800 dark:text-white mb-6">{{ __('Order Summary') }}</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>{{ __('Items') }} ({{ $this->cartItems->sum('quantity') }})</span>
                            <span class="tabular-nums">RM {{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>{{ __('Shipping') }}</span>
                            <span class="text-green-600 font-semibold">{{ __('Free') }}</span>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between">
                            <span class="font-bold text-gray-800 dark:text-white text-lg">{{ __('Total') }}</span>
                            <span class="font-black text-brand-red text-lg tabular-nums">RM {{ number_format($this->subtotal, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}"
                       class="block w-full bg-brand-red text-white text-center py-3.5 rounded-full font-bold text-lg mt-6 hover:bg-red-700 transition-colors shadow-lg hover:shadow-red-500/20 hover:-translate-y-0.5 transition-all duration-300">
                        {{ __('Proceed to Checkout') }}
                    </a>

                    <a href="{{ route('products') }}"
                       class="block w-full text-center py-3 text-sm text-gray-500 dark:text-gray-400 hover:text-brand-red transition-colors mt-3 font-semibold">
                        {{ __('Continue Shopping') }}
                    </a>

                    <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs p-3 rounded-xl text-center">
                        ⚠️ {{ __('DEMO MODE — No actual payment will be processed.') }}
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Empty Cart --}}
        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="text-6xl mb-4" aria-hidden="true">🛒</div>
            <h2 class="text-2xl font-black text-gray-700 dark:text-gray-200 mb-2">{{ __('Your cart is empty') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Explore our products and add something you love!') }}</p>
            <a href="{{ route('products') }}"
               class="inline-block bg-brand-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
                {{ __('Browse Products') }}
            </a>
        </div>
        @endif
    </div>
</div>
