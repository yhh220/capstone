<div>
    {{-- ── Page header ── --}}
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Our Products') }}</h1>
            <p class="text-gray-400">{{ __('Premium car accessories for every need') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ── Sidebar filters ── --}}
            <aside class="w-full lg:w-64 flex-shrink-0" aria-label="Product filters">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 sticky top-24">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg mb-4">{{ __('Filters') }}</h3>

                    {{-- Search --}}
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

                    {{-- Category --}}
                    <div class="mb-5">
                        <label for="product-category" class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Category') }}
                        </label>
                        <select wire:model.live="category"
                                id="product-category"
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="mb-5">
                        <label for="product-sort" class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Sort By') }}
                        </label>
                        <select wire:model.live="sortBy"
                                id="product-sort"
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                            <option value="latest">{{ __('Latest') }}</option>
                            <option value="price_asc">{{ __('Price: Low to High') }}</option>
                            <option value="price_desc">{{ __('Price: High to Low') }}</option>
                            <option value="name">{{ __('Name A-Z') }}</option>
                        </select>
                    </div>

                    {{-- Price range --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">
                            {{ __('Price Range (RM)') }}
                        </label>
                        <div class="flex gap-2">
                            <input wire:model.live.debounce.500ms="minPrice"
                                   type="number"
                                   placeholder="Min"
                                   aria-label="Minimum price"
                                   class="w-1/2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                            <input wire:model.live.debounce.500ms="maxPrice"
                                   type="number"
                                   placeholder="Max"
                                   aria-label="Maximum price"
                                   class="w-1/2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-brand-red transition">
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ── Products grid ── --}}
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $products->total() }} {{ __('products found') }}
                    </p>
                </div>

                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}"
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-shadow overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="relative bg-gray-100 dark:bg-gray-700 h-52 overflow-hidden">
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-6xl" aria-hidden="true">🚗</div>
                            @endif
                            @if($product->is_on_sale)
                            <span class="absolute top-3 left-3 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ __('SALE') }}
                            </span>
                            @endif
                            @if($product->stock === 0)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="bg-white text-gray-800 text-sm font-bold px-3 py-1 rounded-full">
                                    {{ __('Out of Stock') }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">
                                {{ $product->category?->name ?? 'Accessories' }}
                            </div>
                            <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-brand-red transition-colors line-clamp-2">
                                {{ $product->name }}
                            </h3>
                            @if($product->short_description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ $product->short_description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl font-black text-brand-red">RM {{ number_format($product->current_price, 2) }}</span>
                                    @if($product->is_on_sale)
                                    <span class="text-sm text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <span class="text-xs font-medium {{ $product->stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                                    {{ $product->stock > 0 ? __('In Stock') : __('Out of Stock') }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>

                @else
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="text-6xl mb-4" aria-hidden="true">🔍</div>
                    <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-2">{{ __('No products found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Try adjusting your filters or search terms') }}</p>
                    <button wire:click="$set('search', '')"
                            class="mt-4 text-brand-red font-semibold hover:underline">
                        {{ __('Clear filters') }}
                    </button>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
