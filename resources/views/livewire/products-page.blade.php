<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to ask about your product range.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . config('services.store.lat') . ',' . config('services.store.lng');
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4">{{ __('Our Products') }}</h1>
            <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">{{ __('Browse our product showcase, then visit the showroom or contact us on WhatsApp for advice.') }}</p>
        </div>
    </div>

    {{-- 3D configurator promo — opens the configurator modal (see .js-open-configurator) --}}
    <button type="button" class="js-open-configurator group block w-full text-left bg-gradient-to-r from-brand-black via-gray-900 to-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center gap-4">
            <span class="shrink-0 w-12 h-12 rounded-xl bg-brand-red/20 text-brand-red flex items-center justify-center" aria-hidden="true">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5 12 3 3 7.5m18 0L12 12m9-4.5V16.5L12 21m0-9L3 7.5m9 4.5v9M3 7.5V16.5"/></svg>
            </span>
            <span class="flex-1 min-w-0">
                <span class="flex items-center gap-2 font-black text-sm sm:text-base">
                    {{ __('Try Our 3D Configurator') }}
                    <span class="text-[9px] font-bold uppercase tracking-wider bg-brand-red px-1.5 py-0.5 rounded">{{ __('Beta') }}</span>
                </span>
                <span class="block text-xs sm:text-sm text-gray-300 truncate">{{ __('Customise your build and preview it in 3D before you buy.') }}</span>
            </span>
            <span class="shrink-0 inline-flex items-center gap-1.5 text-sm font-bold text-brand-red group-hover:gap-2.5 transition-all">
                <span class="hidden sm:inline">{{ __('Launch') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </span>
        </div>
    </button>

    <div class="max-w-7xl mx-auto px-4 py-10">

        {{-- Search is always visible on mobile; the rest of the filters (category,
             price) stay behind the toggle below. On desktop the sidebar handles search. --}}
        <div class="lg:hidden mb-3">
            <label for="product-search-mobile" class="sr-only">{{ __('Search') }}</label>
            <input wire:model.live.debounce.300ms="search"
                   id="product-search-mobile"
                   type="search"
                   placeholder="{{ __('Search products...') }}"
                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-red transition placeholder-gray-400 dark:placeholder-gray-500">
        </div>

        {{-- Mobile filter toggle (hidden once sidebar appears at md) --}}
        <div class="md:hidden mb-4">
            <button id="filter-toggle"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm w-full justify-between"
                    aria-expanded="false"
                    aria-controls="filter-sidebar">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    {{ __('Filters') }}
                </span>
                <svg id="filter-chevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            <aside id="filter-sidebar" class="hidden md:block w-full md:w-52 lg:w-64 flex-shrink-0" aria-label="{{ __('Product filters') }}">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 lg:sticky lg:top-20">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg mb-4">{{ __('Find products') }}</h3>

                    {{-- Desktop-only: on mobile the always-visible search above is used instead. --}}
                    <div class="mb-5 hidden lg:block">
                        <label for="product-search" class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Search') }}
                        </label>
                        <input wire:model.live.debounce.300ms="search"
                               id="product-search"
                               type="search"
                               placeholder="{{ __('Search products...') }}"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition placeholder-gray-400 dark:placeholder-gray-500">
                    </div>

                    <div class="mb-5">
                        <label for="product-category" class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Category') }}
                        </label>
                        <select wire:model.live="category"
                                id="product-category"
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price filtering only shown when prices are visible (online shopping on) --}}
                    @if($shoppingEnabled)
                    <div class="mb-5">
                        <label class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Price Range (RM)') }}
                        </label>
                        <div class="flex gap-2">
                            <input wire:model.live.debounce.400ms="minPrice"
                                   type="number"
                                   min="0"
                                   aria-label="{{ __('Minimum price (RM)') }}"
                                   placeholder="{{ __('Min') }}"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                            <input wire:model.live.debounce.400ms="maxPrice"
                                   type="number"
                                   min="0"
                                   aria-label="{{ __('Maximum price (RM)') }}"
                                   placeholder="{{ __('Max') }}"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                        </div>
                    </div>
                    @endif

                    <div class="space-y-3 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ $whatsAppUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-whatsapp btn-md btn-shine w-full">
                            {{ __('Ask on WhatsApp') }}
                        </a>
                        <a href="{{ $mapUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-secondary btn-md w-full">
                            {{ __('Visit the showroom') }}
                        </a>
                    </div>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm"
                       wire:loading.remove wire:target="search,category,minPrice,maxPrice,nextPage,previousPage,gotoPage">
                        {{ $products->total() }} {{ __('products found') }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm"
                       wire:loading wire:target="search,category,minPrice,maxPrice,nextPage,previousPage,gotoPage">
                        {{ __('Loading...') }}
                    </p>
                </div>

                {{-- Skeleton cards shown only during filter/search/pagination loading --}}
                <div wire:loading.grid wire:target="search,category,minPrice,maxPrice,nextPage,previousPage,gotoPage"
                     class="grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" style="display: none;" aria-hidden="true">
                    @for($i = 0; $i < 6; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col">
                        <div class="block flex-1">
                            <div class="relative w-full h-52 skeleton !rounded-none"></div>
                            <div class="p-4 pb-3">
                                {{-- Category --}}
                                <div class="skeleton h-3 w-1/3 mb-2"></div>
                                {{-- Title --}}
                                <div class="skeleton h-4 w-full mb-1"></div>
                                <div class="skeleton h-4 w-4/5 mb-3"></div>
                                {{-- Description --}}
                                <div class="skeleton h-3 w-full mb-1"></div>
                                <div class="skeleton h-3 w-5/6 mb-3"></div>
                                {{-- Price --}}
                                <div class="skeleton h-5 w-1/2 mt-1 mb-1"></div>
                                {{-- Stock --}}
                                <div class="skeleton h-3 w-1/3 mt-2"></div>
                            </div>
                        </div>
                        <div class="px-4 pb-4 flex gap-2">
                            <div class="skeleton h-9 flex-1 !rounded-lg"></div>
                            <div class="skeleton h-9 flex-1 !rounded-lg"></div>
                        </div>
                    </div>
                    @endfor
                </div>

                <div id="products-grid" wire:loading.remove wire:target="search,category,minPrice,maxPrice,nextPage,previousPage,gotoPage">
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    @php
                        $productWaUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I\'m interested in ' . $product->name . '. Can you provide more details?');
                    @endphp
                    <div wire:key="product-{{ $product->id }}" class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-shadow overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col">
                        <a href="{{ route('product.show', $product->slug) }}" class="block flex-1">
                            <div class="relative bg-gray-100 dark:bg-gray-700 h-52 overflow-hidden">
                                @if($product->getImageUrl('thumb'))
                                <img src="{{ $product->getImageUrl('thumb') }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 transition-all duration-500 text-gray-300 dark:text-gray-600 group-hover:rotate-[25deg] group-hover:scale-125 group-hover:text-brand-yellow group-hover:-translate-y-2" aria-hidden="true">
                                    <svg class="w-16 h-16 drop-shadow-sm transition-all duration-500" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path><path d="M5 3v4"></path><path d="M19 17v4"></path><path d="M3 5h4"></path><path d="M17 19h4"></path></svg>
                                </div>
                                @endif
                                @if($shoppingEnabled && $product->sale_price && $product->sale_price < $product->price)
                                <div class="absolute top-3 left-3 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ __('SALE') }}
                                </div>
                                @endif
                            </div>
                            <div class="p-4 pb-3">
                                <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">
                                    {{ $product->category?->name ?? __('Accessories') }}
                                </div>
                                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-brand-red transition-colors line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                @if($product->short_description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ $product->short_description }}</p>
                                @endif
                                {{-- Price display --}}
                                @if($shoppingEnabled)
                                <div class="flex items-center gap-2 mt-1">
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="text-brand-red font-black text-lg">RM {{ number_format($product->sale_price, 2) }}</span>
                                        <span class="text-gray-400 line-through text-sm">RM {{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="text-brand-red font-black text-lg">RM {{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                @if($product->stock > 5)
                                    <div class="text-xs mt-1 text-green-500">{{ __('In Stock') }}</div>
                                @elseif($product->stock > 0)
                                    <div class="text-xs mt-1 text-amber-500">{{ __('Only :n left', ['n' => $product->stock]) }}</div>
                                @else
                                    <div class="text-xs mt-1 text-amber-500">{{ __('On backorder · ships in ~:days days', ['days' => (int) setting('BACKORDER_DAYS', 7)]) }}</div>
                                @endif
                                @endif
                            </div>
                        </a>
                        <div class="px-4 pb-4 flex gap-2">
                            @if($shoppingEnabled)
                                <button wire:click="addToCart({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="addToCart({{ $product->id }})"
                                        class="btn btn-primary btn-sm btn-shine flex-1">
                                    <svg wire:loading.remove wire:target="addToCart({{ $product->id }})" class="icon-sm group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                                    <svg wire:loading wire:target="addToCart({{ $product->id }})" class="icon-sm animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    {{ __('Add to Cart') }}
                                </button>
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="btn btn-secondary btn-sm flex-1">
                                    {{ __('Details') }}
                                </a>
                            @else
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="btn btn-secondary btn-sm flex-1">
                                    {{ __('Details') }}
                                </a>
                                <a href="{{ $productWaUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-whatsapp btn-sm btn-shine flex-1"
                                   aria-label="{{ __('Enquire about') }} {{ $product->name }} {{ __('on WhatsApp') }}">
                                    <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{-- Override Livewire's default scrollTo ('body') — scrolling the whole
                         <body> into view lands somewhere unpredictable on long pages; scroll
                         to the top of the grid itself so the new page's results are visible. --}}
                    {{ $products->links(data: ['scrollTo' => '#products-grid']) }}
                </div>
                @else
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 group">
                    <div class="flex justify-center text-gray-300 dark:text-gray-600 mb-6" aria-hidden="true">
                        <svg class="w-16 h-16 group-hover:scale-125 group-hover:text-brand-yellow transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-2">{{ __('No products found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Try adjusting your category or search terms') }}</p>
                    <button wire:click="$set('search', '')"
                            class="mt-4 text-brand-red font-semibold hover:underline">
                        {{ __('Clear search') }}
                    </button>
                </div>
                @endif
                </div>{{-- end wire:loading.remove --}}
            </div>
        </div>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('filter-toggle');
            var sidebar = document.getElementById('filter-sidebar');
            var chevron = document.getElementById('filter-chevron');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var open = sidebar.classList.contains('hidden');
                sidebar.classList.toggle('hidden', !open);
                chevron.style.transform = open ? 'rotate(180deg)' : '';
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        })();
    </script>

    {{-- 3D CAR CONFIGURATOR POPUP MODAL --}}
    <div id="configurator-modal" data-model-url="{{ asset('models/3d/car-draco.glb') }}?v={{ @filemtime(public_path('models/3d/car-draco.glb')) ?: 1 }}">
        <!-- Canvas Viewport -->
        <div class="configurator-viewport" id="configurator-viewport">
            <canvas id="configurator-canvas"></canvas>

            <!-- Loading overlay -->
            <div class="configurator-loader" id="configurator-loader">
                <div class="loader-logo">
                    <svg class="w-16 h-16 text-brand-red animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <div class="loader-title">{{ __('WIN WIN 3D STUDIO') }}</div>
                <div class="loader-subtitle">{{ __('Loading 3D Showroom...') }}</div>
                <div class="loader-progress-container">
                    <div class="loader-progress-bar" id="loader-progress-bar"></div>
                </div>
                <div class="loader-percentage" id="loader-percentage">0%</div>
            </div>

            <!-- Overlay controls -->
            <div class="configurator-controls-overlay">
                <button id="close-configurator-btn" class="circle-btn" title="{{ __('Close Configurator') }}" aria-label="{{ __('Close Configurator') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <button id="camera-reset-btn" class="circle-btn camera-btn" title="{{ __('Reset View') }}" aria-label="{{ __('Reset View') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
                <button id="toggle-doors-btn" class="pill-btn" title="{{ __('Open Doors') }}" aria-label="{{ __('Open Doors') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ __('Open Doors') }}</span>
                </button>
                <button id="toggle-interior-pos-btn" class="pill-btn" title="{{ __('Passenger View') }}" aria-label="{{ __('Passenger View') }}" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>{{ __('Passenger View') }}</span>
                </button>
                <button id="toggle-view-btn" class="pill-btn" title="{{ __('Interior View') }}" aria-label="{{ __('Interior View') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>{{ __('Interior View') }}</span>
                </button>
            </div>

            <!-- Fade Overlay for Cinematic Transition -->
            <div id="configurator-fade-overlay" class="fade-overlay"></div>

            <!-- Viewport Interaction Help -->
            <div class="viewport-help">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                <span>{{ __('Drag to rotate | Scroll to zoom') }}</span>
            </div>
        </div>

        <!-- Customizer Sidebar Panel -->
        <div class="configurator-sidebar">
            <div class="sidebar-header">
                <h2>{{ __('3D CONFIGURATOR') }} <span class="beta-badge">{{ __('Beta') }}</span></h2>
                <p>{{ __('Customize your premium sports build') }}</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="configurator-tabs">
                <div class="tabs grid grid-cols-3 gap-2 md:gap-3 mb-4" style="max-width: 600px; margin: 0 auto 1rem auto;">
                    <button class="tab-btn active" data-tab="paint">{{ __('Paint') }}</button>
                    <button class="tab-btn" data-tab="rims">{{ __('Rims') }}</button>
                    <button class="tab-btn" data-tab="spoilers">{{ __('Spoilers') }}</button>
                    <button class="tab-btn" data-tab="bumpers">{{ __('Bumpers') }}</button>
                    <button class="tab-btn" data-tab="tint">{{ __('Tint') }}</button>
                    <button class="tab-btn" data-tab="dashcams">{{ __('Dash Camera') }}</button>
                </div>
            </div>

            <!-- Sidebar Scrollable Sections -->
            <div class="sidebar-content">
                <!-- Section 1: Paint Color -->
                <div class="sidebar-section active" id="section-paint">
                    <div class="section-title">{{ __('Choose Body Color') }}</div>
                    <div class="color-picker-grid">
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-color="red" style="background-color: #c8413d;"></button>
                            <span class="color-swatch-label">{{ __('Red') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-color="yellow" style="background-color: #facc15;"></button>
                            <span class="color-swatch-label">{{ __('Yellow') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-color="blue" style="background-color: #2563eb;"></button>
                            <span class="color-swatch-label">{{ __('Blue') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-color="grey" style="background-color: #4b5563;"></button>
                            <span class="color-swatch-label">{{ __('Grey') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-color="black" style="background-color: #0f172a;"></button>
                            <span class="color-swatch-label">{{ __('Black') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-color="silver" style="background-color: #cbd5e1;"></button>
                            <span class="color-swatch-label">{{ __('Silver') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch active" data-color="white" style="background-color: #f8fafc; border: 1.5px solid rgba(255,255,255,0.4);"></button>
                            <span class="color-swatch-label">{{ __('White') }}</span>
                        </div>
                    </div>

                    <!-- Choose Rim Color -->
                    <div class="section-title mt-6">{{ __('Choose Rim Color') }}</div>
                    <div class="color-picker-grid">
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch active" data-rim-color="default" style="background: linear-gradient(135deg, #333338, #a1a1aa); border: 1.5px solid rgba(255,255,255,0.4);"></button>
                            <span class="color-swatch-label">{{ __('Default') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-rim-color="black" style="background-color: #111111;"></button>
                            <span class="color-swatch-label">{{ __('Black') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-rim-color="white" style="background-color: #f8fafc; border: 1.5px solid rgba(255,255,255,0.4);"></button>
                            <span class="color-swatch-label">{{ __('White') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-rim-color="silver" style="background-color: #cbd5e1;"></button>
                            <span class="color-swatch-label">{{ __('Silver') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-rim-color="bronze" style="background-color: #a87c43;"></button>
                            <span class="color-swatch-label">{{ __('Bronze') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-rim-color="darkgold" style="background-color: #8a7345;"></button>
                            <span class="color-swatch-label">{{ __('Dark Gold') }}</span>
                        </div>
                    </div>

                    <!-- Choose Brake Color -->
                    <div class="section-title mt-6">{{ __('Choose Brake Color') }}</div>
                    <div class="color-picker-grid">
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch active" data-brake-color="red" style="background-color: #c8413d;"></button>
                            <span class="color-swatch-label">{{ __('Red') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-brake-color="blue" style="background-color: #2563eb;"></button>
                            <span class="color-swatch-label">{{ __('Blue') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-brake-color="yellow" style="background-color: #facc15;"></button>
                            <span class="color-swatch-label">{{ __('Yellow') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-brake-color="white" style="background-color: #f8fafc; border: 1.5px solid rgba(255,255,255,0.4);"></button>
                            <span class="color-swatch-label">{{ __('White') }}</span>
                        </div>
                        <div class="color-swatch-wrapper">
                            <button class="color-swatch" data-brake-color="black" style="background-color: #0c0c0e;"></button>
                            <span class="color-swatch-label">{{ __('Black') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Rims -->
                <div class="sidebar-section" id="section-rims">
                    <div class="section-title">{{ __('Choose Rims') }}</div>
                    <div class="options-grid">
                        <div class="option-card active" data-category="rims" data-item="rim7">
                            <span class="option-name">{{ __('Sport Rims (Default)') }}</span>
                        </div>
                        <div class="option-card" data-category="rims" data-item="rim1">
                            <span class="option-name">{{ __('Vossen CV3 Style') }}</span>
                        </div>
                        <div class="option-card" data-category="rims" data-item="rim2">
                            <span class="option-name">{{ __('BBS Super RS Style') }}</span>
                        </div>
                        <div class="option-card" data-category="rims" data-item="rim3">
                            <span class="option-name">{{ __('Rotiform LAS-R Style') }}</span>
                        </div>
                        <div class="option-card" data-category="rims" data-item="rim4">
                            <span class="option-name">{{ __('HRE P101 Style') }}</span>
                        </div>
                        <div class="option-card" data-category="rims" data-item="rim5">
                            <span class="option-name">{{ __('Advan Racing GT Style') }}</span>
                        </div>
                        <div class="option-card" data-category="rims" data-item="rim6">
                            <span class="option-name">{{ __('TE37 Black Edition') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Spoilers / Wings -->
                <div class="sidebar-section" id="section-spoilers">
                    <div class="section-title">{{ __('Choose Spoiler') }}</div>
                    <div class="options-grid">
                        <div class="option-card active" data-category="spoilers" data-item="wing4">
                            <span class="option-name">{{ __('Integrated Lip (Default)') }}</span>
                        </div>
                        <div class="option-card" data-category="spoilers" data-item="wing1">
                            <span class="option-name">{{ __('Carbon Fiber High Wing') }}</span>
                        </div>
                        <div class="option-card" data-category="spoilers" data-item="wing2">
                            <span class="option-name">{{ __('GT Performance Wing') }}</span>
                        </div>
                        <div class="option-card" data-category="spoilers" data-item="wing3">
                            <span class="option-name">{{ __('Sleek Ducktail Wing') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Front Bumpers -->
                <div class="sidebar-section" id="section-bumpers">
                    <div class="section-title">{{ __('Choose Front Bumper') }}</div>
                    <div class="options-grid">
                        <div class="option-card active" data-category="bumpers" data-item="bumperF3">
                            <span class="option-name">{{ __('Standard Sport (Default)') }}</span>
                        </div>
                        <div class="option-card" data-category="bumpers" data-item="bumperF2">
                            <span class="option-name">{{ __('Widebody Spec Bumper') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Window Tint -->
                <div class="sidebar-section" id="section-tint">
                    <div class="section-title">{{ __('Choose Window Tint Level') }}</div>
                    <div class="options-grid">
                        <div class="option-card active" data-tint="100">
                            <span class="option-name">{{ __('100% (Fully Transparent)') }}</span>
                        </div>
                        <div class="option-card" data-tint="70">
                            <span class="option-name">{{ __('70% Tint') }}</span>
                        </div>
                        <div class="option-card" data-tint="50">
                            <span class="option-name">{{ __('50% Tint') }}</span>
                        </div>
                        <div class="option-card" data-tint="35">
                            <span class="option-name">{{ __('35% Tint') }}</span>
                        </div>
                        <div class="option-card" data-tint="15">
                            <span class="option-name">{{ __('15% Tint') }}</span>
                        </div>
                        <div class="option-card" data-tint="5">
                            <span class="option-name">{{ __('5% Tint (Darkest)') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Dash Cameras -->
                <div class="sidebar-section" id="section-dashcams">
                    <div class="section-title">{{ __('Choose Dash Camera') }}</div>
                    <div class="options-grid">
                        <div class="option-card active" data-category="dashcams" data-item="dashcam0">
                            <span class="option-name">{{ __('None (Default)') }}</span>
                        </div>
                        <div class="option-card" data-category="dashcams" data-item="dashcam1">
                            <span class="option-name">{{ __('Mohawk') }}</span>
                        </div>
                        <div class="option-card" data-category="dashcams" data-item="dashcam2">
                            <span class="option-name">{{ __('70mai') }}</span>
                        </div>
                        <div class="option-card" data-category="dashcams" data-item="dashcam3">
                            <span class="option-name">{{ __('DDPAI') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Summary Panel -->
            <div class="summary-panel">
                <div class="summary-list">
                    <div class="summary-row">
                        <span>{{ __('Body Color') }}</span>
                        <span id="summary-color-name">{{ __('Chalk White') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Rim Color') }}</span>
                        <span id="summary-rim-color">{{ __('Default') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Brake Color') }}</span>
                        <span id="summary-brake-color">{{ __('Ember Red') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Selected Rims') }}</span>
                        <span id="summary-rims">{{ __('Sport Rims (Default)') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Selected Spoiler') }}</span>
                        <span id="summary-spoiler">{{ __('Integrated Lip (Default)') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Front Bumper') }}</span>
                        <span id="summary-bumper">{{ __('Standard Sport (Default)') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Window Tint') }}</span>
                        <span id="summary-window-tint">100%</span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('Dash Camera') }}</span>
                        <span id="summary-dashcam">{{ __('None') }}</span>
                    </div>
                </div>

                <button id="enquire-config-btn" class="btn-shine-custom" data-phone="{{ $storePhoneRaw ?? '60123456789' }}">
                    <svg fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.446L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.967C16.58 2.023 14.12 1 11.997 1 6.558 1 2.13 5.371 2.127 10.8c-.001 1.764.482 3.483 1.398 5.017l-.982 3.582 3.504-.945zM17.47 14.28c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                    {{ __('Enquire Configuration') }}
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        @vite('resources/css/configurator.css')
    @endpush

    @push('scripts')
        @vite('resources/js/configurator-loader.js')
    @endpush
</div>
