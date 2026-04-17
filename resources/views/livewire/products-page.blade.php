<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to ask about your product range.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Our Products') }}</h1>
            <p class="text-gray-400">{{ __('Browse our product showcase, then visit the showroom or contact us on WhatsApp for advice.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">

        {{-- Mobile filter toggle --}}
        <div class="lg:hidden mb-4">
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

        <div class="flex flex-col lg:flex-row gap-8">
            <aside id="filter-sidebar" class="hidden lg:block w-full lg:w-64 flex-shrink-0" aria-label="Product filters">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 lg:sticky lg:top-24">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg mb-4">{{ __('Find products') }}</h3>

                    <div class="mb-5">
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
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Price Range (RM)') }}
                        </label>
                        <div class="flex gap-2">
                            <input wire:model.live.debounce.400ms="minPrice"
                                   type="number"
                                   min="0"
                                   placeholder="{{ __('Min') }}"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                            <input wire:model.live.debounce.400ms="maxPrice"
                                   type="number"
                                   min="0"
                                   placeholder="{{ __('Max') }}"
                                   class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                        </div>
                    </div>

                    <div class="space-y-3 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ $whatsAppUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="block w-full bg-brand-red text-white text-center px-4 py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                            {{ __('Ask on WhatsApp') }}
                        </a>
                        <a href="{{ $mapUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="block w-full border border-gray-200 dark:border-gray-600 text-center px-4 py-3 rounded-xl font-semibold text-gray-700 dark:text-gray-200 hover:border-brand-red hover:text-brand-red transition-colors">
                            {{ __('Visit the showroom') }}
                        </a>
                    </div>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $products->total() }} {{ __('products found') }}
                    </p>
                </div>

                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    @php
                        $productWaUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I\'m interested in ' . $product->name . '. Can you provide more details?');
                    @endphp
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-shadow overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col">
                        <a href="{{ route('product.show', $product->slug) }}" class="block flex-1">
                            <div class="relative bg-gray-100 dark:bg-gray-700 h-52 overflow-hidden">
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-6xl" aria-hidden="true">🚗</div>
                                @endif
                            </div>
                            <div class="p-4 pb-3">
                                <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">
                                    {{ $product->category?->name ?? 'Accessories' }}
                                </div>
                                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-brand-red transition-colors line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                                @if($product->short_description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ $product->short_description }}</p>
                                @endif
                            </div>
                        </a>
                        <div class="px-4 pb-4 flex gap-2">
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="flex-1 text-center text-sm font-semibold text-brand-red border border-brand-red rounded-lg py-2 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                {{ __('Details') }}
                            </a>
                            <a href="{{ $productWaUrl }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex-1 text-center text-sm font-semibold bg-brand-red text-white rounded-lg py-2 hover:bg-red-700 transition-colors"
                               aria-label="{{ __('Enquire about') }} {{ $product->name }} {{ __('on WhatsApp') }}">
                                WhatsApp
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
                @else
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="text-6xl mb-4" aria-hidden="true">🔍</div>
                    <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-2">{{ __('No products found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Try adjusting your category or search terms') }}</p>
                    <button wire:click="$set('search', '')"
                            class="mt-4 text-brand-red font-semibold hover:underline">
                        {{ __('Clear search') }}
                    </button>
                </div>
                @endif
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
</div>
