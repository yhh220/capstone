<div>
    {{-- ── Hero ── --}}
    <section class="hero-gradient text-white py-24 relative overflow-hidden" aria-label="Hero">
        <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
            <div class="absolute top-10 left-10 w-64 h-64 bg-brand-yellow rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-brand-red rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <div class="inline-block bg-brand-yellow text-brand-black text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                    {{ __('#1 Store') }}
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-black mb-6 leading-tight">
                    {{ __('Upgrade Your') }}
                    <span class="text-brand-yellow">{{ __('Ride') }}</span>
                    {{ __('Like a Pro') }}
                </h1>
                <p class="text-base sm:text-lg text-gray-300 mb-8 leading-relaxed">
                    {{ __("Win Win Car Studio Accessories — Premium quality car accessories to make your vehicle stand out. From seat covers to audio systems, we've got everything you need.") }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('products') }}"
                       class="bg-brand-red text-white px-8 py-3 rounded-full font-bold text-lg hover:bg-red-700 transition-colors shadow-lg">
                        {{ __('Shop Now →') }}
                    </a>
                    <a href="{{ route('about') }}"
                       class="border-2 border-white text-white px-8 py-3 rounded-full font-bold text-lg hover:bg-white hover:text-brand-black transition-colors">
                        {{ __('About Us') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Stats bar ── --}}
    <section class="bg-brand-red text-white py-6" aria-label="Statistics">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-3xl font-black" aria-label="500+ Products">500+</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Products') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-black" aria-label="10,000+ Happy Customers">10K+</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Happy Customers') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-black" aria-label="5 star average rating">5★</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Average Rating') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-black" aria-label="10+ Years Experience">10+</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Years Experience') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Categories ── --}}
    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="categories-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="categories-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('Shop by Category') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Explore our wide range of car accessories') }}</p>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $cat)
                <a href="{{ route('products', ['category' => $cat->id]) }}"
                   class="group bg-gray-50 dark:bg-gray-700 border-2 border-transparent hover:border-brand-red rounded-xl p-4 text-center transition-all">
                    <div class="w-14 h-14 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-red group-hover:text-white transition-colors text-2xl" aria-hidden="true">
                        🚗
                    </div>
                    <div class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $cat->name }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $cat->products_count }} items</div>
                </a>
                @endforeach
            </div>
            @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach(['Seat Covers', 'Audio Systems', 'Dash Cams', 'LED Lights', 'Car Mats', 'Air Fresheners'] as $cat)
                <div class="bg-gray-50 dark:bg-gray-700 border-2 border-transparent hover:border-brand-red rounded-xl p-4 text-center transition-all cursor-pointer">
                    <div class="w-14 h-14 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 text-2xl" aria-hidden="true">🚗</div>
                    <div class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $cat }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ── Featured Products ── --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="featured-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 id="featured-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('Featured Products') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Our top picks for your vehicle') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm">
                    {{ __('View All →') }}
                </a>
            </div>

            @if($featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredProducts as $product)
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
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $product->category?->name ?? 'Accessories' }}</div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-brand-red transition-colors">
                            {{ $product->name }}
                        </h3>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(range(1,6) as $i)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="bg-gray-100 dark:bg-gray-700 h-52 flex items-center justify-center text-6xl" aria-hidden="true">🚗</div>
                    <div class="p-4">
                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">Category</div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Sample Product {{ $i }}</h3>
                        <span class="text-xl font-black text-brand-red">RM 99.00</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ── Why Choose Us ── --}}
    <section class="py-16 bg-brand-black text-white" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="why-heading" class="text-3xl sm:text-4xl font-black mb-3">
                    {{ __('Why Choose') }} <span class="text-brand-yellow">Win Win</span>?
                </h2>
                <p class="text-gray-400">{{ __('We go above and beyond for every customer') }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['🏆', __('Premium Quality'),  __('Only the best materials and brands for your vehicle.')],
                    ['🚚', __('Fast Delivery'),     __('Quick shipping to your doorstep across Malaysia.')],
                    ['💰', __('Best Prices'),       __('Competitive pricing without compromising quality.')],
                    ['🛠️', __('Expert Advice'),     __('Our team helps you find exactly what you need.')],
                ] as [$icon, $title, $desc])
                <div class="text-center p-6 bg-gray-800 rounded-2xl hover:bg-gray-700 transition-colors">
                    <div class="text-5xl mb-4" aria-hidden="true">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── New Arrivals ── --}}
    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="arrivals-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 id="arrivals-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('New Arrivals') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Fresh stock just landed') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm">
                    {{ __('See All →') }}
                </a>
            </div>

            @if($newArrivals->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($newArrivals as $product)
                <a href="{{ route('product.show', $product->slug) }}"
                   class="group bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="bg-gray-100 dark:bg-gray-600 h-40 flex items-center justify-center overflow-hidden">
                        @if($product->image)
                        <img src="{{ Storage::url($product->image) }}"
                             alt="{{ $product->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                        <span class="text-5xl" aria-hidden="true">🚗</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-sm text-gray-800 dark:text-gray-200 group-hover:text-brand-red transition-colors line-clamp-2">
                            {{ $product->name }}
                        </div>
                        <div class="text-brand-red font-bold mt-1">RM {{ number_format($product->current_price, 2) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(range(1,4) as $i)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden">
                    <div class="bg-gray-100 dark:bg-gray-600 h-40 flex items-center justify-center text-5xl" aria-hidden="true">🚗</div>
                    <div class="p-3">
                        <div class="font-semibold text-sm text-gray-800 dark:text-gray-200">New Product {{ $i }}</div>
                        <div class="text-brand-red font-bold mt-1">RM 79.00</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ── Testimonials ── --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="testimonials-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('What Our Customers Say') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Real reviews from real car enthusiasts') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['Ahmad Rizal',   'KL',       "Best car accessories store I've ever visited! Great quality and fast delivery. My car looks amazing now!", 5],
                    ['Siti Nurul',    'Selangor',  'Very helpful staff and excellent product selection. The dash cam I bought is top notch. Highly recommended!', 5],
                    ['Tan Wei Ming',  'Penang',    'Amazing service and affordable prices. The seat covers fit perfectly. Will definitely buy again!', 5],
                ] as [$name, $city, $review, $rating])
                <article class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex text-brand-yellow text-xl mb-3" aria-label="{{ $rating }} out of 5 stars" role="img">
                        {{ str_repeat('★', $rating) }}
                    </div>
                    <blockquote class="text-gray-600 dark:text-gray-400 italic mb-4 leading-relaxed text-sm">"{{ $review }}"</blockquote>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-red text-white rounded-full flex items-center justify-center font-bold flex-shrink-0" aria-hidden="true">
                            {{ substr($name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $name }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $city }}</div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA banner ── --}}
    <section class="py-16 bg-brand-red text-white" aria-labelledby="cta-heading">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 id="cta-heading" class="text-3xl sm:text-4xl font-black mb-4">
                {{ __('Ready to Upgrade Your Car?') }}
            </h2>
            <p class="text-lg opacity-90 mb-8">
                {{ __('Browse our extensive collection of premium car accessories. Free shipping on orders above RM150!') }}
            </p>
            <a href="{{ route('products') }}"
               class="inline-block bg-brand-yellow text-brand-black px-10 py-4 rounded-full font-black text-lg hover:bg-yellow-300 transition-colors shadow-lg">
                {{ __('Shop Now — Free Shipping Over RM150') }}
            </a>
        </div>
    </section>
</div>
