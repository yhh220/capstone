<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Checkout') }}</h1>
            <p class="text-gray-400">
                @if($step === 1) {{ __('Step 1: Your Details') }}
                @elseif($step === 2) {{ __('Step 2: Payment') }}
                @else {{ __('Order Confirmed!') }}
                @endif
            </p>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-3xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                @foreach([1 => __('Details'), 2 => __('Payment'), 3 => __('Confirmed')] as $num => $label)
                <div class="flex items-center gap-2 {{ $step >= $num ? 'text-brand-red' : 'text-gray-400' }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black
                                {{ $step >= $num ? 'bg-brand-red text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">
                        @if($step > $num) ✓ @else {{ $num }} @endif
                    </div>
                    <span class="text-sm font-semibold hidden sm:inline">{{ $label }}</span>
                </div>
                @if($num < 3)
                <div class="flex-1 h-0.5 mx-2 {{ $step > $num ? 'bg-brand-red' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- Step 1: Customer Info --}}
        @if($step === 1)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-xl font-black text-gray-800 dark:text-white mb-6">{{ __('Delivery Information') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Full Name') }} *</label>
                    <input wire:model="customerName" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('customerName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email') }} *</label>
                    <input wire:model="customerEmail" type="email" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('customerEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone') }} *</label>
                    <input wire:model="customerPhone" type="tel" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('customerPhone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Street Address') }} *</label>
                    <input wire:model="street" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('street') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('City') }} *</label>
                    <input wire:model="city" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Postcode') }} *</label>
                    <input wire:model="postcode" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('postcode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('State') }} *</label>
                    <select wire:model="state" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                        @foreach(['Selangor','Kuala Lumpur','Johor','Penang','Perak','Pahang','Negeri Sembilan','Melaka','Kedah','Kelantan','Terengganu','Perlis','Sabah','Sarawak','Putrajaya','Labuan'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Order Notes') }}</label>
                    <input wire:model="orderNotes" type="text" placeholder="{{ __('Optional') }}" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="mt-8 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <h3 class="font-bold text-gray-800 dark:text-white mb-3 text-sm">{{ __('Order Summary') }}</h3>
                @foreach($this->cartItems as $item)
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 py-1">
                    <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <span class="tabular-nums">RM {{ number_format(($item->product->current_price ?? 0) * $item->quantity, 2) }}</span>
                </div>
                @endforeach
                <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2 flex justify-between font-bold text-gray-800 dark:text-white">
                    <span>{{ __('Total') }}</span>
                    <span class="text-brand-red tabular-nums">RM {{ number_format($this->subtotal, 2) }}</span>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('cart') }}" class="px-6 py-3 border border-gray-200 dark:border-gray-600 rounded-full font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('← Back to Cart') }}
                </a>
                <button wire:click="goToStep2" class="flex-1 bg-brand-red text-white py-3 rounded-full font-bold text-lg hover:bg-red-700 transition-colors">
                    {{ __('Continue to Payment →') }}
                </button>
            </div>
        </div>

        {{-- Step 2: Mock Payment --}}
        @elseif($step === 2)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-xl font-black text-gray-800 dark:text-white mb-6">{{ __('Payment Method') }}</h2>

            <div class="space-y-3">
                <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors
                    {{ $paymentMethod === 'online_banking' ? 'border-brand-red bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                    <input type="radio" wire:model="paymentMethod" value="online_banking" class="accent-brand-red">
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-white">🏦 {{ __('Online Banking') }}</div>
                        <div class="text-xs text-gray-500">{{ __('FPX / Internet Banking') }}</div>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors
                    {{ $paymentMethod === 'cod' ? 'border-brand-red bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                    <input type="radio" wire:model="paymentMethod" value="cod" class="accent-brand-red">
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-white">💵 {{ __('Cash on Delivery') }}</div>
                        <div class="text-xs text-gray-500">{{ __('Pay when you receive') }}</div>
                    </div>
                </label>
            </div>

            {{-- Demo Notice --}}
            <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 text-center">
                <div class="text-amber-600 dark:text-amber-400 font-bold text-sm mb-1">⚠️ DEMO MODE</div>
                <div class="text-amber-600 dark:text-amber-400 text-xs">{{ __('No actual payment will be processed. This is a prototype demonstration.') }}</div>
            </div>

            {{-- Total --}}
            <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 flex justify-between items-center">
                <span class="font-bold text-gray-800 dark:text-white text-lg">{{ __('Total to pay') }}</span>
                <span class="font-black text-brand-red text-2xl tabular-nums">RM {{ number_format($this->subtotal, 2) }}</span>
            </div>

            <div class="flex gap-3 mt-6">
                <button wire:click="goBack" class="px-6 py-3 border border-gray-200 dark:border-gray-600 rounded-full font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('← Back') }}
                </button>
                <button wire:click="placeOrder"
                        wire:loading.attr="disabled"
                        class="flex-1 bg-brand-red text-white py-3 rounded-full font-bold text-lg hover:bg-red-700 transition-colors disabled:opacity-50 relative">
                    <span wire:loading.remove wire:target="placeOrder">{{ __('Place Order (Demo)') }} →</span>
                    <span wire:loading wire:target="placeOrder" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ __('Processing...') }}
                    </span>
                </button>
            </div>
        </div>

        {{-- Step 3: Confirmation --}}
        @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center" data-aos="zoom-in">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-2">{{ __('Order Confirmed!') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Thank you for your purchase. A confirmation email has been sent.') }}</p>

            @if($order)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 text-left max-w-md mx-auto space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Order Number') }}</span>
                    <span class="font-bold text-gray-800 dark:text-white">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Tracking Number') }}</span>
                    <span class="font-bold text-gray-800 dark:text-white">{{ $order->tracking_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Total') }}</span>
                    <span class="font-black text-brand-red">RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Status') }}</span>
                    <span class="font-bold text-green-600">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8">
                <a href="{{ route('track-order') }}" class="bg-brand-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
                    {{ __('Track Your Order') }}
                </a>
                <a href="{{ route('products') }}" class="border-2 border-gray-200 dark:border-gray-600 px-8 py-3 rounded-full font-bold text-gray-700 dark:text-gray-300 hover:border-brand-red hover:text-brand-red transition-colors">
                    {{ __('Continue Shopping') }}
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
