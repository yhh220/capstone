<div>
    {{-- ── Breadcrumb ── --}}
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

            {{-- ── Product image ── --}}
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

            {{-- ── Product info ── --}}
            <div>
                <div class="text-sm text-brand-red font-semibold mb-2">
                    {{ $product->category?->name ?? 'Accessories' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-4">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <div class="flex items-center flex-wrap gap-3 mb-6">
                    <span class="text-3xl sm:text-4xl font-black text-brand-red">
                        RM {{ number_format($product->current_price, 2) }}
                    </span>
                    @if($product->is_on_sale)
                    <span class="text-xl text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                    <span class="bg-brand-red text-white text-sm font-bold px-2 py-1 rounded-full">{{ __('SALE') }}</span>
                    @endif
                </div>

                {{-- Short description --}}
                @if($product->short_description)
                <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">{{ $product->short_description }}</p>
                @endif

                {{-- Stock status --}}
                <div class="flex items-center gap-2 mb-6">
                    <span class="w-3 h-3 rounded-full flex-shrink-0 {{ $product->stock > 0 ? 'bg-green-500' : 'bg-red-500' }}"
                          aria-hidden="true"></span>
                    <span class="text-sm font-semibold {{ $product->stock > 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                        {{ $product->stock > 0
                            ? __('In Stock') . ' (' . $product->stock . ' available)'
                            : __('Out of Stock') }}
                    </span>
                </div>

                @if($product->stock > 0)
                {{-- Quantity selector --}}
                <div class="flex items-center gap-4 mb-6">
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Quantity:</span>
                    <div class="flex items-center border-2 border-gray-200 dark:border-gray-600 rounded-full overflow-hidden"
                         role="group" aria-label="Quantity selector">
                        <button wire:click="decrementQty"
                                class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-lg font-bold text-gray-700 dark:text-gray-300"
                                aria-label="Decrease quantity">−</button>
                        <span class="px-4 py-2 font-bold text-lg min-w-[3rem] text-center text-gray-800 dark:text-gray-200"
                              aria-live="polite" aria-atomic="true">{{ $quantity }}</span>
                        <button wire:click="incrementQty"
                                class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-lg font-bold text-gray-700 dark:text-gray-300"
                                aria-label="Increase quantity">+</button>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <button class="flex-1 bg-brand-red text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-red-700 transition-colors">
                        Add to Cart
                    </button>
                    <a href="{{ route('contact') }}"
                       class="flex-1 border-2 border-brand-black dark:border-white text-brand-black dark:text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-black hover:text-white dark:hover:bg-white dark:hover:text-brand-black transition-colors text-center">
                        Enquire Now
                    </a>
                </div>
                @else
                <div class="mb-8">
                    <a href="{{ route('contact') }}"
                       class="inline-block border-2 border-brand-red text-brand-red py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-red hover:text-white transition-colors">
                        Notify When Available
                    </a>
                </div>
                @endif

                {{-- Meta info --}}
                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($product->sku)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">SKU:</span>
                        {{ $product->sku }}
                    </div>
                    @endif
                    @if($product->category)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Category:</span>
                        {{ $product->category->name }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Full description ── --}}
        @if($product->description)
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-2xl font-black text-brand-black dark:text-white mb-4">Product Description</h2>
            <div class="text-gray-600 dark:text-gray-400 leading-relaxed prose dark:prose-invert max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
        @endif

        {{-- ── Related products ── --}}
        @if($related->count() > 0)
        <div class="mt-12" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-8">
                Related Products
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
                        <div class="text-brand-red font-bold text-sm mt-1">RM {{ number_format($item->current_price, 2) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
