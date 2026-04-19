<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('My Orders') }}</h1>
            <p class="text-gray-400">{{ __('View all your past and current orders') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-10">
        @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                {{-- Order Header --}}
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
                            {{ ucfirst($order->status) }}
                        </span>
                        <span class="font-black text-brand-red tabular-nums">RM {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                {{-- Order Items --}}
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

                {{-- Order Footer --}}
                <div class="bg-gray-50 dark:bg-gray-700/30 px-5 py-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <div>
                        {{ __('Tracking') }}: <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $order->tracking_number }}</span>
                    </div>
                    <a href="{{ route('track-order') }}" class="text-brand-red font-bold hover:underline">
                        {{ __('Track Order') }} →
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
        @else
        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="text-6xl mb-4" aria-hidden="true">📦</div>
            <h2 class="text-2xl font-black text-gray-700 dark:text-gray-200 mb-2">{{ __('No orders yet') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Start shopping and your orders will appear here!') }}</p>
            <a href="{{ route('products') }}" class="inline-block bg-brand-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
                {{ __('Browse Products') }}
            </a>
        </div>
        @endif
    </div>
</div>
