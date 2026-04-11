<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to learn more about your products.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    <x-compatibility-checker />

    <section class="hero-gradient text-white py-24 relative overflow-hidden" aria-label="Hero">
        <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
            <div class="absolute top-10 left-10 w-64 h-64 bg-brand-yellow rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-brand-red rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <div class="inline-block bg-brand-yellow/90 backdrop-blur-sm text-brand-black text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-5 shadow-sm transform transition-transform hover:scale-105 cursor-default">
                    {{ __('Product showcase and showroom experience') }}
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-black mb-6 leading-tight drop-shadow-md">
                    {{ __('Discover accessories') }}
                    <span class="text-brand-yellow relative inline-block">
                        {{ __('for your car') }}
                        <!-- Subtle underline stroke animation -->
                        <div class="absolute -bottom-1 left-0 w-full h-1 bg-brand-yellow rounded-full transform origin-left scale-x-0 transition-transform duration-1000 ease-out" x-intersect="$el.classList.remove('scale-x-0')"></div>
                    </span>
                    {{ __('with confidence') }}
                </h1>
                <p class="text-base sm:text-lg text-gray-200 mb-8 leading-relaxed max-w-xl font-medium">
                    {{ __('Browse our featured products online, then visit our showroom or contact us on WhatsApp for recommendations, fitting advice, and availability.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products') }}"
                       class="group inline-flex items-center justify-center px-8 py-4 bg-brand-yellow text-brand-black font-extrabold rounded-full transition-all duration-300 ease-out shadow-lg hover:shadow-brand-yellow/30 hover:-translate-y-1 hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-brand-yellow/50">
                        {{ __('Browse Products') }}
                        <!-- Lucide Animated Arrow (Moves right on hover) -->
                        <svg class="w-5 h-5 ml-2 transform transition-transform duration-300 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ $mapUrl }}"
                       target="_blank" rel="noopener noreferrer"
                       class="group inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-full border border-white/20 transition-all duration-300 ease-out shadow-md hover:bg-white/20 hover:border-white/40 hover:-translate-y-1 hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-white/20">
                        <!-- Lucide Animated Map Pin (Bounces on hover) -->
                        <svg class="w-5 h-5 mr-2 transform transition-transform duration-300 group-hover:-translate-y-1 group-hover:text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ __('Visit Showroom') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-brand-red text-white py-6" aria-label="Statistics">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-3xl font-black">500+</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Product options') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-black">10K+</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Customers served') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-black">1:1</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Showroom guidance') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-black">10+</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Years Experience') }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="categories-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="categories-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('Browse by Category') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Explore what is available before you visit or enquire') }}</p>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                <a href="{{ route('products', ['category' => $category->id]) }}"
                   class="group bg-gray-50 dark:bg-gray-700 border-2 border-transparent hover:border-brand-red rounded-xl p-4 text-center transition-all">
                    <div class="w-14 h-14 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-red group-hover:text-white transition-colors text-2xl" aria-hidden="true">
                        🚗
                    </div>
                    <div class="font-semibold text-sm text-gray-800 dark:text-gray-200">{{ $category->name }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $category->products_count }} {{ __('products') }}</div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="featured-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12 gap-4">
                <div>
                    <h2 id="featured-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('Featured Products') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Popular products customers often ask about in store') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm">
                    {{ __('See Full Showcase') }}
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
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $product->category?->name ?? 'Accessories' }}</div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-brand-red transition-colors">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ $product->short_description ?: __('View details and enquire for suitability, pricing, and installation.') }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <section class="py-16 bg-brand-black text-white" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="why-heading" class="text-3xl sm:text-4xl font-black mb-3">
                    {{ __('Why Choose') }} <span class="text-brand-yellow">Win Win</span>?
                </h2>
                <p class="text-gray-400">{{ __('Built to create trust before the customer even steps into the store') }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['🏆', __('Curated products'), __('We highlight proven accessories customers regularly ask for.')],
                    ['📍', __('Real showroom'), __('Customers can inspect products and discuss options in person.')],
                    ['💬', __('WhatsApp support'), __('Ask questions quickly before making the trip to the store.')],
                    ['🛠', __('Installation advice'), __('Our team helps match products to your vehicle and needs.')],
                ] as [$icon, $title, $description])
                <div class="text-center p-6 bg-gray-800 rounded-2xl hover:bg-gray-700 transition-colors">
                    <div class="text-5xl mb-4" aria-hidden="true">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="arrivals-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12 gap-4">
                <div>
                    <h2 id="arrivals-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('Latest Highlights') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Newly added products worth checking before your visit') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm">
                    {{ __('Browse Products') }}
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
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Tap to view details and enquire') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="testimonials-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('What Our Customers Say') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Real feedback about the showroom and consultation experience') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $testimonial)
                <article class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="flex text-brand-yellow text-xl mb-3" aria-label="{{ $testimonial['rating'] ?? $testimonial->rating }} out of 5 stars" role="img">
                        {{ str_repeat('★', $testimonial['rating'] ?? $testimonial->rating) }}
                    </div>
                    <blockquote class="text-gray-600 dark:text-gray-400 italic mb-4 leading-relaxed text-sm">"{{ $testimonial['message'] ?? $testimonial->message }}"</blockquote>
                    <div class="flex items-center gap-3">
                        @if(!empty($testimonial['image']) || !empty($testimonial->image))
                            <img src="{{ Storage::url($testimonial['image'] ?? $testimonial->image) }}" alt="{{ $testimonial['name'] ?? $testimonial->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0" loading="lazy">
                        @else
                            <div class="w-10 h-10 bg-brand-red text-white rounded-full flex items-center justify-center font-bold flex-shrink-0" aria-hidden="true">
                                {{ substr($testimonial['name'] ?? $testimonial->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $testimonial['name'] ?? $testimonial->name }}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $testimonial['location'] ?? $testimonial->location }}</div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 bg-brand-red text-white" aria-labelledby="cta-heading">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 id="cta-heading" class="text-3xl sm:text-4xl font-black mb-4">
                {{ __('Ready to plan your visit?') }}
            </h2>
            <p class="text-lg opacity-90 mb-8">
                {{ __('Use the website to explore our products, then visit the showroom or message us on WhatsApp for faster assistance.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $whatsAppUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-block bg-brand-yellow text-brand-black px-10 py-4 rounded-full font-black text-lg hover:bg-yellow-300 transition-colors shadow-lg">
                    {{ __('Open WhatsApp') }}
                </a>
                <a href="{{ $mapUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-block border-2 border-white px-10 py-4 rounded-full font-black text-lg hover:bg-white hover:text-brand-red transition-colors">
                    {{ __('Get Store Directions') }}
                </a>
            </div>
        </div>
    </section>
</div>
