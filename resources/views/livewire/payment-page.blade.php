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
            <a href="{{ route('account') }}" wire:navigate class="btn btn-primary !rounded-xl uppercase tracking-widest font-black text-sm">{{ __('View My Orders') }}</a>
            <a href="{{ route('products') }}" wire:navigate class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 uppercase tracking-widest font-black text-sm hover:border-brand-red hover:text-brand-red transition-colors">{{ __('Continue Shopping') }}</a>
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
        <a href="{{ route('products') }}" wire:navigate class="btn btn-primary !rounded-xl uppercase tracking-widest font-black text-sm">{{ __('Back to Products') }}</a>
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
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h2 class="font-black text-gray-800 dark:text-white">{{ __('Order') }} <span class="font-mono">{{ $order->order_number }}</span></h2>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $order->payment_method }}</span>
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

        {{-- Demo / testing notice --}}
        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 px-4 py-3 text-amber-700 dark:text-amber-300">
            <div class="flex items-center gap-2 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                {{ __('FOR DEMO / TESTING ONLY') }}
            </div>
            <div class="mt-1 text-xs text-amber-700/80 dark:text-amber-300/80">{{ __('This is a prototype — no real payment is charged and no goods are shipped.') }}</div>
        </div>

        {{-- Pay — instant client-side submit lock (x-bind:disabled) on top of the
             wire:loading disable, so a double-click can't fire pay() twice even
             before the network round-trip starts. --}}
        <button wire:click="pay" wire:loading.attr="disabled" wire:target="pay"
                x-data="{ paying: false }" x-on:click="paying = true" x-bind:disabled="paying"
                class="btn btn-primary btn-shine w-full !py-4 !rounded-xl uppercase tracking-widest font-black disabled:opacity-80 disabled:cursor-not-allowed">
            <span x-show="!paying">{{ __('Pay Now') }} · RM {{ number_format($order->total_amount, 2) }}</span>
            <span x-show="paying">{{ __('Processing payment...') }}</span>
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
