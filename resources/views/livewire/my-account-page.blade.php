<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('My Account') }}</h1>
            <p class="text-gray-400">{{ __('Your orders and service bookings in one place') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-10">

        {{-- Tabs --}}
        <div class="flex flex-wrap gap-2 mb-8" role="tablist">
            @if($shoppingEnabled)
            <button wire:click="setTab('orders')" role="tab"
                    class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors
                           {{ $tab === 'orders' ? 'bg-brand-red text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                {{ __('Orders') }}
            </button>
            @endif
            <button wire:click="setTab('bookings')" role="tab"
                    class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors
                           {{ $tab === 'bookings' ? 'bg-brand-red text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                {{ __('Bookings') }}
            </button>
        </div>

        {{-- Skeleton while switching tabs / paginating --}}
        <div wire:loading.flex wire:target="setTab, nextPage, previousPage, gotoPage, cancelBooking" class="flex-col space-y-4" aria-hidden="true">
            @for($i = 0; $i < 3; $i++)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="space-y-2">
                        <div class="skeleton h-5 w-40"></div>
                        <div class="skeleton h-3 w-32"></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="skeleton h-6 w-20 !rounded-full"></div>
                        <div class="skeleton h-5 w-16"></div>
                    </div>
                </div>
                <div class="px-5 py-3 space-y-2">
                    <div class="skeleton h-4 w-3/4"></div>
                    <div class="skeleton h-4 w-1/2"></div>
                </div>
            </div>
            @endfor
        </div>

        <div wire:loading.remove wire:target="setTab, nextPage, previousPage, gotoPage, cancelBooking">

        {{-- ════════ ORDERS ════════ --}}
        @if($tab === 'orders' && $orders !== null)
            @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-5 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <div class="font-black text-gray-800 dark:text-white text-lg">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                   ($order->status === 'shipped' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                                   'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400')) }}">
                                {{ __(ucfirst($order->status)) }}
                            </span>
                            <span class="font-black text-brand-red tabular-nums">RM {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="px-5 py-3">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center py-2 text-sm {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-700/50' : '' }}">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 dark:text-gray-300">{{ $item->product_name }}</span>
                                <span class="text-gray-400">× {{ $item->quantity }}</span>
                            </div>
                            <span class="font-semibold text-gray-700 dark:text-gray-300 tabular-nums">RM {{ number_format($item->subtotal, 2) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/30 px-5 py-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <div>
                            {{ __('Order Number') }}: <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $order->order_number }}</span>
                            @if($order->payment_status === 'paid')
                                · <span class="text-green-600 dark:text-green-400 font-bold">{{ __('Paid') }}</span>
                            @elseif($order->isCod() && $order->status !== 'cancelled')
                                · <span class="text-blue-600 dark:text-blue-400 font-bold">{{ __('Cash on Delivery') }}</span>
                            @elseif($order->isAwaitingPayment())
                                · <span class="text-amber-600 dark:text-amber-400 font-bold">{{ __('Awaiting payment') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            @if($order->isAwaitingPayment())
                            <a href="{{ route('payment', $order->order_number) }}" class="text-brand-red font-bold hover:underline">{{ __('Pay now') }} <span aria-hidden="true">→</span></a>
                            @endif
                            @if($order->payment_status === 'paid')
                            <a href="{{ route('invoice.show', $order->order_number) }}" class="text-brand-red font-bold hover:underline">{{ __('Invoice') }}</a>
                            @endif
                            <a href="{{ route('track-order') }}" class="text-gray-500 dark:text-gray-400 hover:text-brand-red font-bold">{{ __('Track Order') }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $orders->links() }}</div>
            @else
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 group">
                <div class="flex justify-center text-gray-300 dark:text-gray-600 mb-6" aria-hidden="true">
                    <svg class="w-20 h-20 group-hover:scale-125 group-hover:text-brand-yellow transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-700 dark:text-gray-200 mb-2">{{ __('No orders yet') }}</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Start shopping and your orders will appear here!') }}</p>
                <a href="{{ route('products') }}" class="group relative inline-flex items-center gap-2 bg-brand-red text-white px-8 py-3 rounded-full font-bold transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-1 active:scale-95">
                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                    <span class="relative z-10">{{ __('Browse Products') }}</span>
                </a>
            </div>
            @endif
        @endif

        {{-- ════════ BOOKINGS ════════ --}}
        @if($tab === 'bookings' && $bookings !== null)
            @if(session('booking_success'))
            <div class="flex items-center gap-2 mb-5 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('booking_success') }}</p>
            </div>
            @endif

            @if($bookings->count() > 0)
            <div class="space-y-4">
                @foreach($bookings as $booking)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-5 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <div class="font-black text-gray-800 dark:text-white text-lg">{{ $booking->reference }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $booking->service?->name ?? __('Service') }}</div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $booking->status === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                               ($booking->status === 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                               ($booking->status === 'confirmed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                               'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400')) }}">
                            {{ __(ucfirst($booking->status)) }}
                        </span>
                    </div>

                    <div class="px-5 py-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ optional($booking->start_at)->format('D, d M Y · g:i A') ?? optional($booking->preferred_date)->format('D, d M Y') }}
                        </div>
                        @if($booking->vehicle_model || $booking->vehicle_plate)
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 text-brand-red shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 17h14M5 17a2 2 0 0 1-2-2v-3l2-5h14l2 5v3a2 2 0 0 1-2 2M5 17v2M19 17v2"/></svg>
                            {{ trim(($booking->vehicle_model ?? '') . ' ' . ($booking->vehicle_plate ?? '')) }}
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/30 px-5 py-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <div>
                            {{ __('Reference') }}: <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $booking->reference }}</span>
                        </div>
                        @if(in_array($booking->status, ['pending', 'confirmed'], true))
                        <button type="button"
                                @click="$store.confirm.ask(@js(__('Cancel this booking?')), () => $wire.cancelBooking({{ $booking->id }}))"
                                class="text-red-600 dark:text-red-400 font-bold hover:underline">
                            {{ __('Cancel Booking') }}
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $bookings->links() }}</div>
            @else
            <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 group">
                <div class="flex justify-center text-gray-300 dark:text-gray-600 mb-6" aria-hidden="true">
                    <svg class="w-20 h-20 group-hover:scale-125 group-hover:text-brand-yellow transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-700 dark:text-gray-200 mb-2">{{ __('No bookings yet') }}</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Book a service and it will show up here.') }}</p>
                <a href="{{ route('booking') }}" class="group relative inline-flex items-center gap-2 bg-brand-red text-white px-8 py-3 rounded-full font-bold transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-1 active:scale-95">
                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                    <span class="relative z-10">{{ __('Book a Service') }}</span>
                </a>
            </div>
            @endif
        @endif

        </div>{{-- end wire:loading.remove --}}
    </div>
</div>
