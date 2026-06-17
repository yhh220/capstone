<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
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
                    <label for="co-name" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Full Name') }} *</label>
                    <input wire:model.blur="customerName" id="co-name" type="text" autocomplete="name" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('customerName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="co-email" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email') }} *</label>
                    <input wire:model.blur="customerEmail" id="co-email" type="email" autocomplete="email" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('customerEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="co-phone" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone') }} *</label>
                    <input wire:model.blur="customerPhone" id="co-phone" type="tel" autocomplete="tel" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('customerPhone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="co-street" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Street Address') }} *</label>
                    <input wire:model.blur="street" id="co-street" type="text" autocomplete="street-address" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('street') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="co-city" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('City') }} *</label>
                    <input wire:model.blur="city" id="co-city" type="text" autocomplete="address-level2" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="co-postcode" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Postcode') }} *</label>
                    <input wire:model.blur="postcode" id="co-postcode" type="text" autocomplete="postal-code" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                    @error('postcode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="co-state" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('State') }} *</label>
                    <select wire:model="state" id="co-state" autocomplete="address-level1" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                        @foreach(['Selangor','Kuala Lumpur','Johor','Penang','Perak','Pahang','Negeri Sembilan','Melaka','Kedah','Kelantan','Terengganu','Perlis','Sabah','Sarawak','Putrajaya','Labuan'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="co-notes" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Order Notes') }}</label>
                    <input wire:model.blur="orderNotes" id="co-notes" type="text" placeholder="{{ __('Optional') }}" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="mt-8 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <h3 class="font-bold text-gray-800 dark:text-white mb-3 text-sm">{{ __('Order Summary') }}</h3>
                @foreach($this->cartItems as $item)
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 py-1">
                    <span>{{ $item->product?->name ?? __('Product unavailable') }} × {{ $item->quantity }}</span>
                    <span class="tabular-nums">RM {{ number_format(($item->product?->current_price ?? 0) * $item->quantity, 2) }}</span>
                </div>
                @endforeach
                <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2 flex justify-between font-bold text-gray-800 dark:text-white">
                    <span>{{ __('Total') }}</span>
                    <span class="text-brand-red tabular-nums">RM {{ number_format($this->subtotal, 2) }}</span>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <a href="{{ route('cart') }}" class="group flex items-center px-6 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-full font-semibold text-gray-600 dark:text-gray-300 hover:border-brand-red hover:text-brand-red hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-300 hover:-translate-y-0.5 active:scale-95">
                    {{ __('← Back to Cart') }}
                </a>
                <button wire:click="goToStep2"
                        wire:loading.attr="disabled"
                        wire:target="goToStep2"
                        class="group relative inline-flex justify-center items-center gap-2 flex-1 bg-brand-red text-white py-3 rounded-full font-black text-lg transition-all duration-300 shadow-[0_6px_20px_rgb(var(--brand-red-rgb)_/_0.35)] overflow-hidden hover:shadow-[0_10px_30px_rgb(var(--brand-red-rgb)_/_0.5)] hover:-translate-y-1 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                    <span class="relative z-10" wire:loading.remove wire:target="goToStep2">{{ __('Continue to Payment') }}</span>
                    <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:translate-x-1" wire:loading.remove wire:target="goToStep2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="goToStep2">{{ __('Checking...') }}</span>
                </button>
            </div>
        </div>

        {{-- Step 2: Payment (display-only demo — methods are shown but no real charge occurs) --}}
        @elseif($step === 2)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-xl font-black text-gray-800 dark:text-white mb-1">{{ __('Payment Method') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('Choose how you would like to pay') }}</p>

            {{-- Demo notice --}}
            <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/60 rounded-xl p-4">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                <div>
                    <div class="text-amber-700 dark:text-amber-400 font-bold text-sm">{{ __('DEMO MODE') }}</div>
                    <div class="text-amber-700/80 dark:text-amber-400/80 text-xs mt-0.5">{{ __('No actual payment will be processed. This is a prototype demonstration.') }}</div>
                </div>
            </div>

            <div class="space-y-3">
                {{-- FPX Online Banking --}}
                <div class="rounded-xl border-2 transition-colors {{ $paymentMethod === 'fpx' ? 'border-brand-red bg-red-50/50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-600' }}">
                    <label class="flex items-center gap-3 p-4 cursor-pointer">
                        <input type="radio" wire:model.live="paymentMethod" value="fpx" class="accent-brand-red w-4 h-4 shrink-0">
                        <span class="w-11 h-9 rounded-md bg-blue-600 text-white flex items-center justify-center font-black text-[11px] tracking-wide shrink-0">FPX</span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-semibold text-gray-800 dark:text-white">{{ __('FPX Online Banking') }}</span>
                            <span class="block text-xs text-gray-500">{{ __('Pay directly from your bank account') }}</span>
                        </span>
                    </label>
                    @if($paymentMethod === 'fpx')
                    <div class="px-4 pb-4 sm:pl-[4.25rem]">
                        <label for="fpx-bank" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">{{ __('Select your bank') }}</label>
                        <select wire:model="fpxBank" id="fpx-bank" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand-red transition">
                            @foreach($fpxBanks as $bank)
                            <option value="{{ $bank }}">{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                {{-- E-Wallet --}}
                <div class="rounded-xl border-2 transition-colors {{ $paymentMethod === 'ewallet' ? 'border-brand-red bg-red-50/50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-600' }}">
                    <label class="flex items-center gap-3 p-4 cursor-pointer">
                        <input type="radio" wire:model.live="paymentMethod" value="ewallet" class="accent-brand-red w-4 h-4 shrink-0">
                        <span class="w-11 h-9 rounded-md bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M16 14h2"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-semibold text-gray-800 dark:text-white">{{ __('E-Wallet') }}</span>
                            <span class="block text-xs text-gray-500 truncate">Touch 'n Go &middot; GrabPay &middot; ShopeePay &middot; Boost</span>
                        </span>
                    </label>
                    @if($paymentMethod === 'ewallet')
                    <div class="px-4 pb-4 sm:pl-[4.25rem] grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($ewallets as $w)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="ewallet" value="{{ $w }}" class="sr-only peer">
                            <span class="flex items-center justify-center text-center text-xs font-bold leading-tight px-2 py-2.5 rounded-lg border-2 transition-colors h-full
                                        peer-checked:border-brand-red peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-brand-red
                                        border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300">{{ $w }}</span>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Credit / Debit Card --}}
                <div class="rounded-xl border-2 transition-colors {{ $paymentMethod === 'card' ? 'border-brand-red bg-red-50/50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-600' }}">
                    <label class="flex items-center gap-3 p-4 cursor-pointer">
                        <input type="radio" wire:model.live="paymentMethod" value="card" class="accent-brand-red w-4 h-4 shrink-0">
                        <span class="flex gap-1 shrink-0" aria-hidden="true">
                            <span class="w-7 h-9 rounded-md bg-[#1A1F71] text-white flex items-center justify-center font-black text-[8px] italic">VISA</span>
                            <span class="w-7 h-9 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <span class="flex"><span class="w-2.5 h-2.5 rounded-full bg-[#EB001B]"></span><span class="w-2.5 h-2.5 rounded-full bg-[#F79E1B] -ml-1 opacity-90"></span></span>
                            </span>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-semibold text-gray-800 dark:text-white">{{ __('Credit / Debit Card') }}</span>
                            <span class="block text-xs text-gray-500">Visa &middot; Mastercard</span>
                        </span>
                    </label>
                    @if($paymentMethod === 'card')
                    <div class="px-4 pb-4 sm:pl-[4.25rem] space-y-3">
                        <div>
                            <label for="card-number" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">{{ __('Card Number') }}</label>
                            <input id="card-number" type="text" inputmode="numeric" maxlength="19" autocomplete="cc-number" placeholder="1234 5678 9012 3456"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm tracking-wider focus:outline-none focus:border-brand-red transition">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="card-expiry" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">{{ __('Expiry') }}</label>
                                <input id="card-expiry" type="text" inputmode="numeric" maxlength="5" autocomplete="cc-exp" placeholder="MM/YY"
                                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand-red transition">
                            </div>
                            <div>
                                <label for="card-cvv" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">{{ __('CVV') }}</label>
                                <input id="card-cvv" type="text" inputmode="numeric" maxlength="4" autocomplete="cc-csc" placeholder="123"
                                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand-red transition">
                            </div>
                        </div>
                        <div>
                            <label for="card-name" class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">{{ __('Name on Card') }}</label>
                            <input id="card-name" type="text" autocomplete="cc-name" placeholder="{{ __('Full name') }}"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-brand-red transition">
                        </div>
                        <p class="text-xs text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            {{ __('Demo only — card details are not collected or stored.') }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Cash on Delivery --}}
                <div class="rounded-xl border-2 transition-colors {{ $paymentMethod === 'cod' ? 'border-brand-red bg-red-50/50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-600' }}">
                    <label class="flex items-center gap-3 p-4 cursor-pointer">
                        <input type="radio" wire:model.live="paymentMethod" value="cod" class="accent-brand-red w-4 h-4 shrink-0">
                        <span class="w-11 h-9 rounded-md bg-green-600 text-white flex items-center justify-center shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-semibold text-gray-800 dark:text-white">{{ __('Cash on Delivery') }}</span>
                            <span class="block text-xs text-gray-500">{{ __('Pay when you receive') }}</span>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Total --}}
            <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 flex justify-between items-center">
                <span class="font-bold text-gray-800 dark:text-white text-lg">{{ __('Total to pay') }}</span>
                <span class="font-black text-brand-red text-2xl tabular-nums">RM {{ number_format($this->subtotal, 2) }}</span>
            </div>

            <div class="flex gap-3 mt-6">
                <button wire:click="goBack" class="group flex items-center px-6 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-full font-semibold text-gray-600 dark:text-gray-300 hover:border-brand-red hover:text-brand-red hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-300 hover:-translate-y-0.5 active:scale-95">
                    {{ __('← Back') }}
                </button>
                <button wire:click="placeOrder"
                        wire:confirm="{{ __('Demo mode — no real payment will be charged. Place this test order?') }}"
                        wire:loading.attr="disabled"
                        wire:target="placeOrder"
                        class="group relative inline-flex justify-center items-center gap-2 flex-1 bg-brand-red text-white py-3 rounded-full font-black text-lg transition-all duration-300 shadow-[0_6px_20px_rgb(var(--brand-red-rgb)_/_0.35)] overflow-hidden hover:shadow-[0_10px_30px_rgb(var(--brand-red-rgb)_/_0.5)] hover:-translate-y-1 active:scale-95 disabled:opacity-50">
                    <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                    <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:scale-110" wire:loading.remove wire:target="placeOrder" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="relative z-10" wire:loading.remove wire:target="placeOrder">{{ __('Place Order (Demo)') }}</span>
                    <span class="relative z-10 hidden flex items-center justify-center gap-2" wire:loading.class.remove="hidden" wire:target="placeOrder">
                        <svg class="icon-spin w-5 h-5" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ __('Processing...') }}
                    </span>
                </button>
            </div>
        </div>

        {{-- Step 3: Confirmation --}}
        @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 text-center" data-aos="zoom-in">
            <div class="flex justify-center mb-6" aria-hidden="true">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-2">{{ __('Order Confirmed!') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Thank you for your purchase. A confirmation email has been sent.') }}</p>

            @if($order)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 text-left max-w-md mx-auto space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Order Number') }}</span>
                    <span class="font-bold text-gray-800 dark:text-white">{{ $order->order_number }}</span>
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

            <div class="flex flex-col sm:flex-row gap-6 justify-center mt-8">
                <a href="{{ route('track-order') }}"
                   class="group relative inline-flex items-center justify-center gap-3 bg-brand-red text-white px-8 py-4 rounded-full font-black text-base transition-all duration-300 shadow-[0_6px_20px_rgb(var(--brand-red-rgb)_/_0.35)] overflow-hidden hover:shadow-[0_10px_30px_rgb(var(--brand-red-rgb)_/_0.5)] hover:-translate-y-2 active:scale-95">
                    <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                    <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="relative z-10">{{ __('Track Your Order') }}</span>
                </a>
                <a href="{{ route('products') }}"
                   class="group inline-flex items-center justify-center gap-3 border-2 border-gray-200 dark:border-gray-600 px-8 py-4 rounded-full font-black text-base text-gray-700 dark:text-gray-300 hover:border-brand-red hover:text-brand-red hover:-translate-y-2 hover:shadow-lg transition-all duration-300 active:scale-95">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"></path><path d="m12 19-7-7 7-7"/></svg>
                    {{ __('Continue Shopping') }}
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
