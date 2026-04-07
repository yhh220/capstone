<div>
    <!-- Page Header -->
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-black mb-2">Our Products</h1>
            <p class="text-gray-400">Premium car accessories for every need</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="font-bold text-gray-800 text-lg mb-4">Filters</h3>

                    <!-- Search -->
                    <div class="mb-5">
                        <label class="text-sm font-semibold text-gray-600 mb-2 block">Search</label>
                        <input wire:model.live.debounce.300ms="search" type="text"
                               placeholder="Search products..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red">
                    </div>

                    <!-- Category -->
                    <div class="mb-5">
                        <label class="text-sm font-semibold text-gray-600 mb-2 block">Category</label>
                        <select wire:model.live="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="mb-5">
                        <label class="text-sm font-semibold text-gray-600 mb-2 block">Sort By</label>
                        <select wire:model.live="sortBy" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-red">
                            <option value="latest">Latest</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="name">Name A-Z</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="text-sm font-semibold text-gray-600 mb-2 block">Price Range (RM)</label>
                        <div class="flex gap-2">
                            <input wire:model.live.debounce.500ms="minPrice" type="number" placeholder="Min"
                                   class="w-1/2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-brand-red">
                            <input wire:model.live.debounce.500ms="maxPrice" type="number" placeholder="Max"
                                   class="w-1/2 border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-brand-red">
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Results count -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-500 text-sm">{{ $products->total() }} products found</p>
                </div>

                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}"
                       class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition overflow-hidden border border-gray-100">
                        <div class="relative bg-gray-100 h-52 overflow-hidden">
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-6xl">🚗</div>
                            @endif
                            @if($product->is_on_sale)
                            <span class="absolute top-3 left-3 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-full">SALE</span>
                            @endif
                            @if($product->stock === 0)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="bg-white text-gray-800 text-sm font-bold px-3 py-1 rounded-full">Out of Stock</span>
                            </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="text-xs text-gray-400 mb-1">{{ $product->category?->name ?? 'Accessories' }}</div>
                            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-brand-red transition line-clamp-2">{{ $product->name }}</h3>
                            @if($product->short_description)
                            <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $product->short_description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl font-black text-brand-red">RM {{ number_format($product->current_price, 2) }}</span>
                                    @if($product->is_on_sale)
                                    <span class="text-sm text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <span class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>

                @else
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No products found</h3>
                    <p class="text-gray-500">Try adjusting your filters or search terms</p>
                    <button wire:click="$set('search', '')" class="mt-4 text-brand-red font-semibold hover:underline">Clear filters</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
