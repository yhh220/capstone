<div class="max-w-2xl mx-auto px-4 py-10 sm:py-16">

    {{-- ════════ PAID ════════ --}}
    @if($order->payment_status === 'paid')
    <div class="text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 sm:p-12">
        <div class="mx-auto mb-6 flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-500/15 text-green-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h1 class="font-display text-3xl uppercase text-gray-900 dark:text-white mb-2">{{ __('Payment Successful!') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Your order is confirmed — a confirmation email is on its way.') }}</p>
        <p class="font-mono font-bold text-gray-700 dark:text-gray-200 mb-6">{{ $order->order_number }}</p>
        <div class="inline-flex items-center gap-2 text-lg font-black text-brand-red mb-8">{{ __('Total') }}: RM {{ number_format($order->total_amount, 2) }}</div>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('account') }}" wire:navigate class="btn btn-primary btn-md btn-shine !rounded-xl uppercase tracking-widest font-black">{{ __('View My Orders') }}</a>
            <a href="{{ route('products') }}" wire:navigate class="btn btn-secondary btn-md !rounded-xl uppercase tracking-widest font-black">{{ __('Continue Shopping') }}</a>
        </div>
    </div>

    {{-- ════════ EXPIRED / CANCELLED ════════ --}}
    @elseif($order->status === 'cancelled')
    <div class="text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 sm:p-12">
        <div class="mx-auto mb-6 flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-500/15 text-red-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h1 class="font-display text-3xl uppercase text-gray-900 dark:text-white mb-2">{{ __('Payment Time Expired') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">{{ __('This order was cancelled because payment was not completed in time. The items have been released back to stock — please order again.') }}</p>
        <a href="{{ route('products') }}" wire:navigate class="btn btn-primary btn-md btn-shine !rounded-xl uppercase tracking-widest font-black">{{ __('Back to Products') }}</a>
    </div>

    {{-- ════════ AWAITING PAYMENT ════════ --}}
    @else
    <div x-data="paymentTimer({{ $order->secondsUntilExpiry() }})" class="space-y-6">

        {{-- Countdown --}}
        <div class="rounded-2xl border-2 border-brand-red/30 bg-brand-red/5 dark:bg-brand-red/10 p-5 text-center">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-brand-red">{{ __('Complete payment within') }}</p>
            <p class="font-display text-5xl text-brand-red tabular-nums mt-2" x-text="display">--:--</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('The order is held with your stock reserved. It auto-cancels when the timer runs out.') }}</p>
        </div>

        {{-- Order summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4" x-data="{ changing: false }">
                <h2 class="font-black text-gray-800 dark:text-white">{{ __('Order') }} <span class="font-mono">{{ $order->order_number }}</span></h2>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $order->payment_method }}</span>
                    @if($stripeEnabled)
                    <button type="button" x-on:click="changing = !changing" class="text-xs font-bold text-brand-red hover:underline">{{ __('Change') }}</button>
                    @endif
                </div>
                @if($stripeEnabled)
                {{-- Escape hatch when the chosen method fails on Stripe's side:
                     switch without cancelling the order or losing the timer. --}}
                <div x-show="changing" x-cloak style="display:none;" class="w-full flex flex-wrap gap-2 pt-1">
                    @foreach($methodOptions as $value => $label)
                    <button type="button" wire:click="changePaymentMethod({{ Js::from($value) }})" x-on:click="changing = false"
                            wire:loading.attr="disabled"
                            class="px-3 py-1.5 rounded-lg border-2 text-xs font-bold transition-colors {{ $order->payment_method === $value ? 'border-brand-red text-brand-red bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-brand-red hover:text-brand-red' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @foreach($order->items as $item)
            <div class="flex justify-between items-center py-2 text-sm border-b border-gray-50 dark:border-gray-700/50">
                <span class="text-gray-700 dark:text-gray-300">{{ $item->product_name }} <span class="text-gray-400">× {{ $item->quantity }}</span></span>
                <span class="font-semibold text-gray-700 dark:text-gray-300 tabular-nums">RM {{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
            <div class="flex justify-between pt-3 text-sm text-gray-600 dark:text-gray-400"><span>{{ __('Subtotal') }}</span><span class="tabular-nums">RM {{ number_format($order->subtotal, 2) }}</span></div>
            <div class="flex justify-between py-1 text-sm text-gray-600 dark:text-gray-400"><span>{{ __('Shipping') }}</span><span class="tabular-nums">{{ $order->shipping_fee > 0 ? 'RM ' . number_format($order->shipping_fee, 2) : __('Free') }}</span></div>
            <div class="flex justify-between pt-2 mt-1 border-t border-gray-100 dark:border-gray-700 font-black text-lg"><span class="text-gray-800 dark:text-white">{{ __('Total') }}</span><span class="text-brand-red tabular-nums">RM {{ number_format($order->total_amount, 2) }}</span></div>
        </div>

        {{-- Payment-mode notice: Stripe test mode vs pure demo --}}
        @if($isStripeCheckout)
        <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800/40 px-4 py-3 text-indigo-700 dark:text-indigo-300">
            <div class="flex items-center gap-2 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                {{ __('STRIPE TEST MODE') }}
            </div>
            <div class="mt-1 text-xs text-indigo-700/80 dark:text-indigo-300/80">{{ __('You will be redirected to Stripe to complete payment securely. Test payments only — no real money is charged.') }}</div>
        </div>
        @else
        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 px-4 py-3 text-amber-700 dark:text-amber-300">
            <div class="flex items-center gap-2 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                {{ __('FOR DEMO / TESTING ONLY') }}
            </div>
            <div class="mt-1 text-xs text-amber-700/80 dark:text-amber-300/80">{{ __('This is a prototype — no real payment is charged and no goods are shipped.') }}</div>
        </div>
        @endif

        {{-- Stripe reported the payment still settling (FPX/GrabPay can lag the
             redirect). wire:poll re-verifies the session every 10s so the page
             flips to the success card by itself once the bank confirms — no
             manual refresh, and no webhook required locally. --}}
        @if($paymentProcessing)
        <div wire:poll.10s="pollPaymentStatus" class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/40 px-4 py-3 text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
            <svg class="icon-sm icon-spin shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ __('Your payment is being confirmed by the bank. This page will update once it clears — you can also check My Account shortly.') }}
        </div>
        @endif

        {{-- Stripe session creation failed --}}
        @if(session('payment_error'))
        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 px-4 py-3 text-sm font-semibold text-red-700 dark:text-red-300">
            {{ session('payment_error') }}
        </div>
        @endif

        {{-- Pay — wire:loading (not Alpine state) drives the label/spinner swap:
             it is morph-safe, resets itself when the request settles, and can't
             be left stuck by bfcache when the browser navigates back from
             Stripe. wire:loading.attr disables at request start, and the
             server-side settle step is idempotent against double-clicks anyway. --}}
        <button wire:click="pay" wire:loading.attr="disabled" wire:target="pay"
                class="btn {{ $isStripeCheckout ? 'btn-stripe' : 'btn-primary' }} btn-shine w-full !py-4 !rounded-xl uppercase tracking-widest font-black disabled:opacity-80 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="pay" class="flex items-center justify-center gap-2">
                @if($isStripeCheckout)
                    {{ __('Pay with') }}
                    <img src="{{ asset('images/payment/stripe-white.svg') }}" alt="Stripe" class="h-5 w-auto -mx-0.5 mt-px">
                @else
                    <svg class="icon-sm" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    {{ __('Pay Now') }}
                @endif
                · RM {{ number_format($order->total_amount, 2) }}
            </span>
            {{-- style="display:none" keeps this hidden before Livewire boots;
                 Livewire then owns the toggle while a pay() request runs. --}}
            <span wire:loading.flex wire:target="pay" style="display:none" class="items-center justify-center gap-2">
                <svg class="icon-sm icon-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ $isStripeCheckout ? __('Redirecting to Stripe...') : __('Processing payment...') }}
            </span>
        </button>
        <a href="{{ route('account') }}" wire:navigate class="block text-center text-sm text-gray-500 dark:text-gray-400 hover:text-brand-red transition-colors">{{ __('Pay later from My Account') }}</a>

        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-start gap-1.5">
            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg>
            {{ __('Paid orders can be cancelled before shipping under our') }}
            <a href="{{ route('cancellation-refund-policy') }}" target="_blank" class="text-brand-red font-semibold hover:underline">{{ __('Cancellation & Refund Policy') }}</a>.
        </p>
    </div>

    {{-- Register the countdown as a proper Alpine component via @assets (loaded
         once, before Alpine boots) instead of a fragile inline <script> inside the
         Livewire view — which could leave x-data un-initialised until an interaction
         (the "have to scroll before the UI appears" glitch). --}}
    @assets
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('paymentTimer', (seconds) => ({
                left: seconds,
                display: '',
                timer: null,
                init() {
                    this.tick();
                    this.timer = setInterval(() => {
                        this.left--;
                        if (this.left <= 0) {
                            this.left = 0; this.tick();
                            clearInterval(this.timer);
                            this.$wire.expireOrder();
                            return;
                        }
                        this.tick();
                    }, 1000);
                },
                tick() {
                    const m = Math.floor(this.left / 60), s = this.left % 60;
                    this.display = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },
            }));
        });
    </script>
    @endassets
    @endif
</div>
