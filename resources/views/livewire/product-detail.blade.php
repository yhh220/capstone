<div>
    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-3 border-b">
        <div class="max-w-7xl mx-auto px-4 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-brand-red">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products') }}" class="hover:text-brand-red">Products</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">{{ $product->name }}</span>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            <!-- Product Image -->
            <div>
                <div class="bg-gray-100 rounded-2xl h-96 flex items-center justify-center overflow-hidden">
                    @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                    <span class="text-9xl">🚗</span>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div>
                <div class="text-sm text-brand-red font-semibold mb-2">{{ $product->category?->name ?? 'Accessories' }}</div>
                <h1 class="text-3xl font-black text-brand-black mb-4">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-4xl font-black text-brand-red">RM {{ number_format($product->current_price, 2) }}</span>
                    @if($product->is_on_sale)
                    <span class="text-xl text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                    <span class="bg-brand-red text-white text-sm font-bold px-2 py-1 rounded-full">SALE</span>
                    @endif
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                <p class="text-gray-600 mb-6 leading-relaxed">{{ $product->short_description }}</p>
                @endif

                <!-- Stock Status -->
                <div class="flex items-center gap-2 mb-6">
                    <span class="w-3 h-3 rounded-full {{ $product->stock > 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <span class="text-sm font-semibold {{ $product->stock > 0 ? 'text-green-700' : 'text-red-700' }}">
                        {{ $product->stock > 0 ? 'In Stock (' . $product->stock . ' available)' : 'Out of Stock' }}
                    </span>
                </div>

                @if($product->stock > 0)
                <!-- Quantity -->
                <div class="flex items-center gap-4 mb-6">
                    <span class="font-semibold text-gray-700">Quantity:</span>
                    <div class="flex items-center border-2 border-gray-200 rounded-full overflow-hidden">
                        <button wire:click="decrementQty" class="px-4 py-2 hover:bg-gray-100 transition text-lg font-bold">−</button>
                        <span class="px-4 py-2 font-bold text-lg min-w-[3rem] text-center">{{ $quantity }}</span>
                        <button wire:click="incrementQty" class="px-4 py-2 hover:bg-gray-100 transition text-lg font-bold">+</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <button class="flex-1 bg-brand-red text-white py-3 px-8 rounded-full font-bold text-lg hover:bg-red-700 transition">
                        Add to Cart
                    </button>
                    <a href="{{ route('contact') }}" class="flex-1 border-2 border-brand-black text-brand-black py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-black hover:text-white transition text-center">
                        Enquire Now
                    </a>
                </div>
                @else
                <div class="mb-8">
                    <a href="{{ route('contact') }}" class="inline-block border-2 border-brand-red text-brand-red py-3 px-8 rounded-full font-bold text-lg hover:bg-brand-red hover:text-white transition">
                        Notify When Available
                    </a>
                </div>
                @endif

                <!-- Meta Info -->
                <div class="border-t border-gray-100 pt-6 space-y-2 text-sm text-gray-500">
                    @if($product->sku)
                    <div><span class="font-semibold text-gray-700">SKU:</span> {{ $product->sku }}</div>
                    @endif
                    @if($product->category)
                    <div><span class="font-semibold text-gray-700">Category:</span> {{ $product->category->name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($product->description)
        <div class="mt-12 bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-2xl font-black text-brand-black mb-4">Product Description</h2>
            <div class="prose max-w-none text-gray-600 leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
        @endif

        <!-- Related Products -->
        @if($related->count() > 0)
        <div class="mt-12">
            <h2 class="text-3xl font-black text-brand-black mb-8">Related Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($related as $item)
                <a href="{{ route('product.show', $item->slug) }}" class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition border border-gray-100">
                    <div class="bg-gray-100 h-40 flex items-center justify-center overflow-hidden">
                        @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                        @else
                        <span class="text-4xl">🚗</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-sm group-hover:text-brand-red transition line-clamp-2">{{ $item->name }}</div>
                        <div class="text-brand-red font-bold text-sm mt-1">RM {{ number_format($item->current_price, 2) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
