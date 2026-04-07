<div>
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-64 h-64 bg-brand-yellow rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-brand-red rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <div class="inline-block bg-brand-yellow text-brand-black text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                    #1 Car Accessories Store
                </div>
                <h1 class="text-5xl md:text-6xl font-black mb-6 leading-tight">
                    Upgrade Your
                    <span class="text-brand-yellow">Ride</span> Like
                    a Pro
                </h1>
                <p class="text-lg text-gray-300 mb-8 leading-relaxed">
                    Win Win Car Studio Accessories — Premium quality car accessories to make your vehicle stand out. From seat covers to audio systems, we've got everything you need.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('products') }}" class="bg-brand-red text-white px-8 py-3 rounded-full font-bold text-lg hover:bg-red-700 transition shadow-lg">
                        Shop Now →
                    </a>
                    <a href="{{ route('about') }}" class="border-2 border-white text-white px-8 py-3 rounded-full font-bold text-lg hover:bg-white hover:text-brand-black transition">
                        About Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="bg-brand-red text-white py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-3xl font-black">500+</div>
                    <div class="text-sm opacity-80 mt-1">Products</div>
                </div>
                <div>
                    <div class="text-3xl font-black">10K+</div>
                    <div class="text-sm opacity-80 mt-1">Happy Customers</div>
                </div>
                <div>
                    <div class="text-3xl font-black">5★</div>
                    <div class="text-sm opacity-80 mt-1">Average Rating</div>
                </div>
                <div>
                    <div class="text-3xl font-black">10+</div>
                    <div class="text-sm opacity-80 mt-1">Years Experience</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-brand-black mb-3">Shop by Category</h2>
                <p class="text-gray-500">Explore our wide range of car accessories</p>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $cat)
                <a href="{{ route('products', ['category' => $cat->id]) }}"
                   class="group bg-gray-50 border-2 border-transparent hover:border-brand-red rounded-xl p-4 text-center transition">
                    <div class="w-14 h-14 bg-brand-red/10 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-red group-hover:text-white transition text-2xl">
                        🚗
                    </div>
                    <div class="font-semibold text-sm text-gray-800">{{ $cat->name }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $cat->products_count }} items</div>
                </a>
                @endforeach
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach(['Seat Covers', 'Audio Systems', 'Dash Cams', 'LED Lights', 'Car Mats', 'Air Fresheners'] as $cat)
                <div class="bg-gray-50 border-2 border-transparent hover:border-brand-red rounded-xl p-4 text-center transition cursor-pointer">
                    <div class="w-14 h-14 bg-brand-red/10 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">🚗</div>
                    <div class="font-semibold text-sm text-gray-800">{{ $cat }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-4xl font-black text-brand-black mb-2">Featured Products</h2>
                    <p class="text-gray-500">Our top picks for your vehicle</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline">View All →</a>
            </div>

            @if($featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredProducts as $product)
                <a href="{{ route('product.show', $product->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition overflow-hidden border border-gray-100">
                    <div class="relative bg-gray-100 h-52 overflow-hidden">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-6xl">🚗</div>
                        @endif
                        @if($product->is_on_sale)
                        <span class="absolute top-3 left-3 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-full">SALE</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-gray-400 mb-1">{{ $product->category?->name ?? 'Accessories' }}</div>
                        <h3 class="font-bold text-gray-800 mb-2 group-hover:text-brand-red transition">{{ $product->name }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-black text-brand-red">RM {{ number_format($product->current_price, 2) }}</span>
                            @if($product->is_on_sale)
                            <span class="text-sm text-gray-400 line-through">RM {{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <!-- Placeholder cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(range(1,6) as $i)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gray-100 h-52 flex items-center justify-center text-6xl">🚗</div>
                    <div class="p-4">
                        <div class="text-xs text-gray-400 mb-1">Category</div>
                        <h3 class="font-bold text-gray-800 mb-2">Sample Product {{ $i }}</h3>
                        <span class="text-xl font-black text-brand-red">RM 99.00</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-brand-black text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black mb-3">Why Choose <span class="text-brand-yellow">Win Win</span>?</h2>
                <p class="text-gray-400">We go above and beyond for every customer</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['🏆', 'Premium Quality', 'Only the best materials and brands for your vehicle.'],
                    ['🚚', 'Fast Delivery', 'Quick shipping to your doorstep across Malaysia.'],
                    ['💰', 'Best Prices', 'Competitive pricing without compromising quality.'],
                    ['🛠️', 'Expert Advice', 'Our team helps you find exactly what you need.'],
                ] as [$icon, $title, $desc])
                <div class="text-center p-6 bg-gray-800 rounded-2xl hover:bg-gray-700 transition">
                    <div class="text-5xl mb-4">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-4xl font-black text-brand-black mb-2">New Arrivals</h2>
                    <p class="text-gray-500">Fresh stock just landed</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline">See All →</a>
            </div>

            @if($newArrivals->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($newArrivals as $product)
                <a href="{{ route('product.show', $product->slug) }}" class="group bg-gray-50 rounded-xl overflow-hidden hover:shadow-lg transition">
                    <div class="bg-gray-100 h-40 flex items-center justify-center overflow-hidden">
                        @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                        @else
                        <span class="text-5xl">🚗</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-sm group-hover:text-brand-red transition">{{ $product->name }}</div>
                        <div class="text-brand-red font-bold mt-1">RM {{ number_format($product->current_price, 2) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(range(1,4) as $i)
                <div class="bg-gray-50 rounded-xl overflow-hidden">
                    <div class="bg-gray-100 h-40 flex items-center justify-center text-5xl">🚗</div>
                    <div class="p-3">
                        <div class="font-semibold text-sm">New Product {{ $i }}</div>
                        <div class="text-brand-red font-bold mt-1">RM 79.00</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-brand-black mb-3">What Our Customers Say</h2>
                <p class="text-gray-500">Real reviews from real car enthusiasts</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['Ahmad Rizal', 'KL', 'Best car accessories store I\'ve ever visited! Great quality and fast delivery. My car looks amazing now!', 5],
                    ['Siti Nurul', 'Selangor', 'Very helpful staff and excellent product selection. The dash cam I bought is top notch. Highly recommended!', 5],
                    ['Tan Wei Ming', 'Penang', 'Amazing service and affordable prices. The seat covers fit perfectly. Will definitely buy again!', 5],
                ] as [$name, $city, $review, $rating])
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex text-brand-yellow text-xl mb-3">{{ str_repeat('★', $rating) }}</div>
                    <p class="text-gray-600 italic mb-4 leading-relaxed">"{{ $review }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-red text-white rounded-full flex items-center justify-center font-bold">
                            {{ substr($name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ $name }}</div>
                            <div class="text-xs text-gray-400">{{ $city }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <section class="py-16 bg-brand-red text-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-black mb-4">Ready to Upgrade Your Car?</h2>
            <p class="text-lg opacity-90 mb-8">Browse our extensive collection of premium car accessories. Free shipping on orders above RM150!</p>
            <a href="{{ route('products') }}" class="inline-block bg-brand-yellow text-brand-black px-10 py-4 rounded-full font-black text-lg hover:bg-yellow-300 transition shadow-lg">
                Shop Now — Free Shipping Over RM150
            </a>
        </div>
    </section>
</div>
