<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Track Your Order') }}</h1>
            <p class="text-gray-400">{{ __('Enter your order number and email to check the status') }}</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-10">
        {{-- Search Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700 mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Order Number') }}</label>
                    <input wire:model="orderNumber" type="text" placeholder="ORD-2026-00001"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    @error('orderNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email Address') }}</label>
                    <input wire:model="email" type="email" placeholder="your@email.com"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <button wire:click="trackOrder"
                    wire:loading.attr="disabled"
                    class="w-full mt-4 bg-brand-red text-white py-3 rounded-full font-bold hover:bg-red-700 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="trackOrder">🔍 {{ __('Track Order') }}</span>
                <span wire:loading wire:target="trackOrder">{{ __('Searching...') }}</span>
            </button>
        </div>

        {{-- Error Message --}}
        @if($searched && $errorMsg)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-6 text-center mb-8">
            <div class="text-4xl mb-3">😔</div>
            <p class="text-red-600 dark:text-red-400 font-semibold">{{ $errorMsg }}</p>
        </div>
        @endif

        {{-- Order Found --}}
        @if($order)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            {{-- Order Info --}}
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-8">
                <div>
                    <h2 class="text-xl font-black text-gray-800 dark:text-white">{{ $order->order_number }}</h2>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Tracking') }}: <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $order->tracking_number }}</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ __('Ordered on') }} {{ $order->created_at->format('d M Y, h:i A') }}</div>
                </div>
                <div class="px-4 py-1.5 rounded-full text-sm font-bold
                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                       ($order->status === 'shipped' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                       'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400')) }}">
                    {{ ucfirst($order->status) }}
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="mb-8">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">{{ __('Order Progress') }}</h3>
                <div class="flex items-center justify-between relative">
                    {{-- Connecting line --}}
                    <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 dark:bg-gray-700 z-0"></div>

                    @php
                        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIdx = array_search($order->status, $statusOrder);
                        if ($currentIdx === false) $currentIdx = -1;
                    @endphp

                    @foreach($this->statusSteps as $i => $step)
                    <div class="relative z-10 flex flex-col items-center" style="width: 25%;">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shadow-sm
                            @if($order->status === 'cancelled' && $step['key'] === 'cancelled')
                                bg-red-500 text-white
                            @elseif($order->status === 'cancelled' && $step['key'] !== 'cancelled')
                                bg-gray-200 dark:bg-gray-700 text-gray-400
                            @elseif($i <= $currentIdx)
                                bg-green-500 text-white
                            @else
                                bg-gray-200 dark:bg-gray-700 text-gray-400
                            @endif
                        ">
                            {{ $step['icon'] }}
                        </div>
                        <div class="text-xs font-semibold mt-2 text-center
                            {{ $i <= $currentIdx ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Order Items --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">{{ __('Items') }}</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center text-sm py-2">
                        <div>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->product_name }}</span>
                            <span class="text-gray-400 ml-2">× {{ $item->quantity }}</span>
                        </div>
                        <span class="font-bold text-gray-800 dark:text-gray-200 tabular-nums">RM {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 dark:border-gray-700 mt-3 pt-3 flex justify-between">
                    <span class="font-bold text-gray-800 dark:text-white text-lg">{{ __('Total') }}</span>
                    <span class="font-black text-brand-red text-lg tabular-nums">RM {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            {{-- Shipping Address --}}
            @if($order->shipping_address)
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6 mt-6">
                <h3 class="font-bold text-gray-800 dark:text-white mb-2">{{ __('Shipping Address') }}</h3>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $order->shipping_address['street'] ?? '' }}<br>
                    {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['postcode'] ?? '' }}<br>
                    {{ $order->shipping_address['state'] ?? '' }}
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
