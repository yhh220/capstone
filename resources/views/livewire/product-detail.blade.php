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
            <div data-aos="fade-right">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl h-80 sm:h-96 flex items-center justify-center overflow-hidden">
                    @if($product->getImageUrl('card'))
                    <img src="{{ $product->getImageUrl('card') }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover rounded-2xl"
                         fetchpriority="high">
                    @elseif($product->getImageUrl())
                    <img src="{{ $product->getImageUrl() }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover rounded-2xl"
                         fetchpriority="high">
                    @elseif($product->image)
                    <img src="{{ Storage::url($product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover rounded-2xl"
                         fetchpriority="high">
                    @else
                    <span class="text-9xl" aria-hidden="true">🚗</span>
                    @endif
                </div>
            </div>

            <div data-aos="fade-left" data-aos-delay="80">
                <div class="text-sm text-brand-red font-semibold mb-2">
                    {{ $product->category?->name ?? 'Accessories' }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-4">
                    {{ $product->name }}
                </h1>
                @if($product->brand)
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('Brand') }}: <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $product->brand }}</span>
                </div>
                @endif

                {{-- Price Section (when shopping is enabled) --}}
                @if($shoppingEnabled)
                <div class="mb-6">
                    <div class="flex items-end gap-3 mb-2">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="text-3xl font-black text-brand-red">RM {{ number_format($product->sale_price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                            <span class="bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ __('SAVE') }} {{ round((1 - $product->sale_price / $product->price) * 100) }}%
                            </span>
                        @else
                            <span class="text-3xl font-black text-brand-red">RM {{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <div class="text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }} font-semibold">
                        @if($product->stock > 0)
                            ✅ {{ __('In Stock') }} ({{ $product->stock }} {{ __('available') }})
                        @else
                            ❌ {{ __('Out of Stock') }}
                        @endif
                    </div>
                </div>

                {{-- Quantity Selector + Add to Cart --}}
                @if($product->stock > 0)
                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-full px-2">
                        <button wire:click="decrementQuantity"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-bold text-lg"
                                aria-label="{{ __('Decrease quantity') }}">
                            −
                        </button>
                        <span class="w-12 text-center font-bold text-gray-800 dark:text-gray-200 tabular-nums"
                              wire:key="qty-{{ $quantity }}">
                            {{ $quantity }}
                        </span>
                        <button wire:click="incrementQuantity"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-bold text-lg"
                                aria-label="{{ __('Increase quantity') }}">
                            +
                        </button>
                    </div>
                    <button wire:click="addToCart"
                            class="flex-1 bg-brand-red text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-red-700 transition-all duration-300 shadow-lg hover:shadow-red-500/20 hover:-translate-y-0.5 active:scale-95">
                        🛒 {{ __('Add to Cart') }}
                    </button>
                </div>
                @else
                <div class="mb-6">
                    <div class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 py-3 px-8 rounded-full font-bold text-lg text-center">
                        {{ __('Out of Stock') }}
                    </div>
                </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <a href="{{ $whatsAppUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex-1 border-2 border-brand-red text-brand-red py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-red hover:text-white transition-colors text-center">
                        {{ __('Ask on WhatsApp') }}
                    </a>
                    <a href="{{ $mapUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex-1 border-2 border-brand-black dark:border-white text-brand-black dark:text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-black hover:text-white dark:hover:bg-white dark:hover:text-brand-black transition-colors text-center">
                        {{ __('Visit the showroom') }}
                    </a>
                </div>
                @else
                {{-- Shopping disabled: show enquiry notice --}}
                <div class="flex items-start gap-2 bg-red-50 dark:bg-red-900/20 text-brand-red px-4 py-2.5 rounded-xl text-sm font-semibold mb-6">
                    <span class="flex-shrink-0 mt-0.5" aria-hidden="true">💬</span>
                    <span>{{ __('Enquire on WhatsApp or visit the showroom for pricing and compatibility.') }}</span>
                </div>

                @if($product->short_description)
                <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">{{ $product->short_description }}</p>
                @endif

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
                @endif

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
                    @if(!empty($product->compatible_vehicles))
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Compatible Vehicles') }}:</span>
                        {{ implode(', ', $product->compatible_vehicles) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($product->has_3d && $product->model_url)
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-2xl font-black text-brand-black dark:text-white">{{ __('3D Viewer') }}</h2>
                <span class="text-xs uppercase tracking-widest text-gray-400">{{ __('Mount Point Ready') }}</span>
            </div>
            <div id="3d-mount-product"
                 data-model-url="{{ $product->model_url }}"
                 data-product-name="{{ $product->name }}"
                 class="min-h-[320px] rounded-2xl bg-gray-100 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center text-center px-6">
                <div>
                    <div class="text-5xl mb-3">3D</div>
                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Interactive product viewer will mount here.') }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Fallback content remains visible until the viewer script is integrated.') }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($translatedDescription)
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Product Overview') }}</h2>
            <div class="text-gray-600 dark:text-gray-400 leading-relaxed prose dark:prose-invert max-w-none">
                {!! nl2br(e($translatedDescription)) !!}
            </div>
        </div>
        @endif

        @if(!empty($product->specs))
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <h2 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Specifications') }}</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($product->specs as $label => $value)
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 px-4 py-3">
                    <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">{{ $label }}</div>
                    <div class="font-semibold text-gray-800 dark:text-gray-100">{{ is_array($value) ? implode(', ', $value) : $value }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($related->count() > 0)
        <div class="mt-12" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white mb-8" data-aos="fade-up">
                {{ __('More Products to Explore') }}
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($related as $i => $item)
                <a href="{{ route('product.show', $item->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700"
                   data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <div class="bg-gray-100 dark:bg-gray-700 h-40 flex items-center justify-center overflow-hidden">
                        @if($item->getImageUrl('thumb'))
                        <img src="{{ $item->getImageUrl('thumb') }}"
                             alt="{{ $item->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @elseif($item->image)
                        <img src="{{ Storage::url($item->image) }}"
                             alt="{{ $item->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
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
