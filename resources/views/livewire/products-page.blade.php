<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to ask about your product range.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
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
                           class="group relative block w-full bg-brand-yellow text-brand-black text-center px-4 py-3 rounded-xl font-black transition-all duration-300 overflow-hidden hover:shadow-lg hover:-translate-y-1 active:scale-95">
                            <span class="absolute inset-0 w-full h-full bg-white/30 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                            <span class="relative z-10">{{ __('Ask on WhatsApp') }}</span>
                        </a>
                        <a href="{{ $mapUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group relative block w-full border-2 border-gray-200 dark:border-gray-600 text-center px-4 py-3 rounded-xl font-bold text-gray-700 dark:text-gray-200 hover:border-brand-red hover:text-brand-red transition-all duration-300 overflow-hidden hover:bg-white dark:hover:bg-gray-800 hover:-translate-y-1 active:scale-95">
                            <span class="absolute inset-0 w-full h-full bg-brand-red/5 -translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out"></span>
                            <span class="relative z-10">{{ __('Visit the showroom') }}</span>
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
                                    {{ $product->category?->name ?? 'Accessories' }}
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
                                <div class="text-xs mt-1 {{ $product->stock > 0 ? 'text-green-500' : 'text-red-400' }}">
                                    {{ $product->stock > 0 ? __('In Stock') . ' (' . $product->stock . ')' : __('Out of Stock') }}
                                </div>
                                @endif
                            </div>
                        </a>
                        <div class="px-4 pb-4 flex gap-2">
                            @if($shoppingEnabled && $product->stock > 0)
                                <button wire:click="addToCart({{ $product->id }})"
                                        class="group relative flex-1 flex justify-center items-center gap-1 text-sm font-bold bg-brand-red text-white w-full rounded-xl py-2.5 transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-0.5 active:scale-95">
                                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                                    <svg class="w-4 h-4 relative z-10 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                                    <span class="relative z-10">{{ __('Add to Cart') }}</span>
                                </button>
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="group flex-1 text-center text-sm font-bold text-gray-700 dark:text-gray-200 border-2 border-gray-200 dark:border-gray-600 rounded-xl py-2.5 hover:border-brand-red hover:text-brand-red hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-300 hover:-translate-y-0.5 active:scale-95">
                                    {{ __('Details') }}
                                </a>
                            @else
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="group flex-1 text-center text-sm font-bold text-gray-700 dark:text-gray-200 border-2 border-gray-200 dark:border-gray-600 rounded-xl py-2.5 hover:border-brand-red hover:text-brand-red hover:bg-red-50 dark:hover:bg-red-900/10 transition-all duration-300 hover:-translate-y-0.5 active:scale-95">
                                    {{ __('Details') }}
                                </a>
                                <a href="{{ $productWaUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="group relative flex-1 flex justify-center items-center gap-1 text-sm font-bold bg-[#25D366] text-white w-full rounded-xl py-2.5 transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(37,211,102,0.4)] hover:-translate-y-0.5 active:scale-95"
                                   aria-label="{{ __('Enquire about') }} {{ $product->name }} {{ __('on WhatsApp') }}">
                                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                                    <svg class="w-4 h-4 relative z-10 group-hover:rotate-[15deg] transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    <span class="relative z-10">WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
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
