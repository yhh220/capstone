<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I\'m interested in ' . $product->name . '. Can you provide more details?');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    <div class="bg-gray-50 dark:bg-gray-900 py-3 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb" role="navigation">
            <ol class="flex items-center flex-wrap gap-1" role="list">
                <li role="listitem">
                    <a href="{{ route('home') }}" class="hover:text-brand-red transition-colors">{{ __('Home') }}</a>
                </li>
                <li role="listitem" aria-hidden="true"><span class="mx-1">/</span></li>
                <li role="listitem">
                    <a href="{{ route('products') }}" class="hover:text-brand-red transition-colors">{{ __('Products') }}</a>
                </li>
                <li role="listitem" aria-hidden="true"><span class="mx-1">/</span></li>
                <li role="listitem" class="text-gray-800 dark:text-gray-200 font-medium truncate max-w-xs" aria-current="page">
                    {{ $product->name }}
                </li>
            </ol>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl h-80 sm:h-96 flex items-center justify-center overflow-hidden">
                    @if($product->image)
                    <img src="{{ Storage::url($product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover rounded-2xl"
                         fetchpriority="high">
                    @else
                    <span class="text-9xl" aria-hidden="true">🚗</span>
                    @endif
                </div>
            </div>

            <div>
                <div class="text-sm text-brand-red font-semibold mb-2">
                    {{ $product->category?->name ?? 'Accessories' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-4">
                    {{ $product->name }}
                </h1>

                <div class="inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/20 text-brand-red px-4 py-2 rounded-full text-sm font-semibold mb-6">
                    <span aria-hidden="true">💬</span>
                    <span>{{ __('Enquire on WhatsApp or visit the showroom for pricing and compatibility.') }}</span>
                </div>

                @if($product->short_description)
                <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">{{ $product->short_description }}</p>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4">
                        <div class="text-xs uppercase tracking-wider text-gray-400 mb-1">{{ __('Category') }}</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $product->category?->name ?? 'Accessories' }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4">
                        <div class="text-xs uppercase tracking-wider text-gray-400 mb-1">{{ __('Best for') }}</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ __('Showroom comparison') }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4">
                        <div class="text-xs uppercase tracking-wider text-gray-400 mb-1">{{ __('Support') }}</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ __('WhatsApp consultation') }}</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex-1 bg-brand-red text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-red-700 transition-colors text-center">
                        {{ __('Ask on WhatsApp') }}
                    </a>
                    <a href="{{ $mapUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex-1 border-2 border-brand-black dark:border-white text-brand-black dark:text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-black hover:text-white dark:hover:bg-white dark:hover:text-brand-black transition-colors text-center">
                        {{ __('Visit the showroom') }}
                    </a>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($product->sku)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">SKU:</span>
                        {{ $product->sku }}
                    </div>
                    @endif
                    @if($product->category)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Category') }}:</span>
                        {{ $product->category->name }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($product->description)
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Product Overview') }}</h2>
            <div class="text-gray-600 dark:text-gray-400 leading-relaxed prose dark:prose-invert max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
        @endif

        @if($related->count() > 0)
        <div class="mt-12" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-8">
                {{ __('More Products to Explore') }}
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($related as $item)
                <a href="{{ route('product.show', $item->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow border border-gray-100 dark:border-gray-700">
                    <div class="bg-gray-100 dark:bg-gray-700 h-40 flex items-center justify-center overflow-hidden">
                        @if($item->image)
                        <img src="{{ Storage::url($item->image) }}"
                             alt="{{ $item->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                        <span class="text-4xl" aria-hidden="true">🚗</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-sm text-gray-800 dark:text-gray-200 group-hover:text-brand-red transition-colors line-clamp-2">
                            {{ $item->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('View details and enquire') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
