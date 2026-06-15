<div>
    @unless($compact)
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Shopping Cart') }}</h1>
            <p class="text-gray-400">{{ __('Review your items before checkout') }}</p>
        </div>
    </div>
    @endunless

    <div class="{{ $compact ? 'px-4 py-5' : 'max-w-7xl mx-auto px-4 py-10' }}">
        @if($this->cartItems->count() > 0)
        <div class="grid grid-cols-1 {{ $compact ? 'gap-5' : 'lg:grid-cols-3 gap-8' }}">
            {{-- Cart Items --}}
            <div class="{{ $compact ? 'space-y-3' : 'lg:col-span-2 space-y-4' }}">
                @foreach($this->cartItems as $item)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 {{ $compact ? '' : 'sm:p-6' }} shadow-sm border border-gray-100 dark:border-gray-700 flex gap-4 items-center" wire:key="cart-{{ $item->id }}">
                    {{-- Product Image --}}
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden flex-shrink-0">
                        @if($item->product?->image)
                            <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600" aria-hidden="true">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="flex-1 min-w-0">
                        @if($item->product)
                        <a href="{{ route('product.show', $item->product->slug) }}" class="font-bold text-gray-800 dark:text-gray-200 hover:text-brand-red transition-colors line-clamp-1">
                            {{ $item->product->name }}
                        </a>
                        @else
                        <div class="font-bold text-gray-800 dark:text-gray-200 line-clamp-1">
                            {{ __('Unavailable product') }}
                        </div>
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            RM {{ number_format($item->product?->current_price ?? 0, 2) }} {{ __('each') }}
                        </div>
                        @if(!$item->product || ($item->product->stock ?? 0) <= 0)
                        <div class="mt-2 text-xs font-semibold text-red-600 dark:text-red-400">
                            {{ __('This item is currently out of stock. Please remove it before checkout.') }}
                        </div>
                        @elseif($item->quantity > $item->product->stock)
                        <div class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-400">
                            {{ __('Only :stock left in stock. Please reduce the quantity before checkout.', ['stock' => $item->product->stock]) }}
                        </div>
                        @elseif($item->product->stock <= 3)
                        <div class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-400">
                            {{ __('Low stock: only :stock left.', ['stock' => $item->product->stock]) }}
                        </div>
                        @endif

                        {{-- Quantity Controls --}}
                        <div class="flex items-center gap-3 mt-3">
                            <x-tooltip text="{{ __('Decrease') }}">
                                <button wire:click="decrementQuantity({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="decrementQuantity({{ $item->id }})"
                                        class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-bold"
                                        aria-label="{{ __('Decrease quantity') }}">
                                    −
                                </button>
                            </x-tooltip>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200 w-8 text-center tabular-nums">
                                {{ $item->quantity }}
                            </span>
                            <x-tooltip text="{{ __('Increase') }}">
                                <button wire:click="incrementQuantity({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="incrementQuantity({{ $item->id }})"
                                        @if($item->product && $item->quantity >= $item->product->stock) disabled @endif
                                        class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-bold disabled:opacity-40 disabled:cursor-not-allowed"
                                        aria-label="{{ __('Increase quantity') }}">
                                    +
                                </button>
                            </x-tooltip>
                        </div>
                    </div>

                    {{-- Item Subtotal + Remove --}}
                    <div class="text-right flex-shrink-0">
                        <div class="font-black text-brand-red text-lg tabular-nums">
                            RM {{ number_format(($item->product->current_price ?? 0) * $item->quantity, 2) }}
                        </div>
                        <button wire:click="removeItem({{ $item->id }})"
                                wire:confirm="{{ __('Remove this item?') }}"
                                wire:loading.attr="disabled"
                                wire:target="removeItem({{ $item->id }})"
                                class="text-xs text-gray-400 hover:text-red-500 transition-colors mt-2 underline"
                                aria-label="{{ __('Remove') }} {{ $item->product?->name ?? __('Unavailable product') }}">
                            {{ __('Remove') }}
                        </button>
                    </div>
                </div>
                @endforeach

                <div class="text-right">
                    <button wire:click="clearCart" wire:confirm="{{ __('Clear all items from your cart?') }}"
                            wire:loading.attr="disabled"
                            wire:target="clearCart"
                            class="text-sm text-gray-500 hover:text-red-500 transition-colors underline disabled:opacity-60">
                        {{ __('Clear Cart') }}
                    </button>
                </div>
            </div>

            {{-- Order Summary --}}
            <div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 {{ $compact ? '' : 'sticky top-24' }}">
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

                    @if($this->hasStockWarnings)
                    <div class="mt-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm font-semibold text-red-700 dark:text-red-300 text-center">
                        {{ __('Please resolve stock warnings before checkout.') }}
                    </div>
                    <span class="block w-full bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-center py-3.5 rounded-full font-bold text-lg mt-3 cursor-not-allowed">
                        {{ __('Checkout unavailable') }}
                    </span>
                    @else
                    <a href="{{ route('checkout') }}"
                       class="group relative overflow-hidden block w-full bg-brand-red text-white text-center py-3.5 rounded-full font-bold text-lg mt-6 transition-all duration-300 shadow-lg hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-0.5 active:scale-95">
                        <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                        <span class="relative z-10">{{ __('Proceed to Checkout') }}</span>
                    </a>
                    @endif

                    <a href="{{ route('products') }}"
                       class="block w-full text-center py-3 text-sm text-gray-500 dark:text-gray-400 hover:text-brand-red transition-colors mt-3 font-semibold">
                        {{ __('Continue Shopping') }}
                    </a>

                    <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs p-3 rounded-xl text-center flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                        {{ __('DEMO MODE — No actual payment will be processed.') }}
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Empty Cart --}}
        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 group">
            <div class="flex justify-center text-gray-300 dark:text-gray-600 mb-6" aria-hidden="true">
                <svg class="w-20 h-20 group-hover:scale-125 group-hover:text-brand-yellow transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-700 dark:text-gray-200 mb-2">{{ __('Your cart is empty') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Explore our products and add something you love!') }}</p>
            <a href="{{ route('products') }}"
               class="group relative inline-flex items-center gap-2 bg-brand-red text-white px-8 py-3 rounded-full font-bold transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-1 active:scale-95">
                <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                <span class="relative z-10">{{ __('Browse Products') }}</span>
            </a>
        </div>
        @endif
    </div>
</div>
