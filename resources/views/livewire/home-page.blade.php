<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to learn more about your products.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    @livewire('compatibility-checker')

    {{-- ── Hero ─────────────────────────────────────────────── --}}
    <section class="hero-gradient text-white py-14 sm:py-20 md:py-24 relative overflow-hidden" aria-label="Hero">
        <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
            <div class="absolute top-10 left-10 w-64 h-64 bg-brand-yellow rounded-full blur-3xl orb-float"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-brand-red rounded-full blur-3xl orb-float-alt"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <div class="hero-reveal inline-block bg-brand-yellow/90 backdrop-blur-sm text-brand-black text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-5 shadow-sm">
                    {{ __('Product showcase and showroom experience') }}
                </div>
                <h1 class="hero-reveal-delay1 text-4xl sm:text-5xl md:text-6xl font-black mb-6 leading-tight drop-shadow-md">
                    {{ __('Discover accessories') }}
                    <span class="text-brand-yellow relative inline-block">
                        {{ __('for your car') }}
                        <div class="absolute -bottom-1 left-0 w-full h-1 bg-brand-yellow rounded-full transform origin-left scale-x-0 transition-transform duration-1000 ease-out" x-intersect="$el.classList.remove('scale-x-0')"></div>
                    </span>
                    {{ __('with confidence') }}
                </h1>
                <p class="hero-reveal-delay2 text-base sm:text-lg text-gray-200 mb-8 leading-relaxed max-w-xl font-medium">
                    {{ __('Browse our featured products online, then visit our showroom or contact us on WhatsApp for recommendations, fitting advice, and availability.') }}
                </p>
                <div class="hero-reveal-delay3 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products') }}"
                       class="group inline-flex items-center justify-center px-8 py-4 bg-brand-yellow text-brand-black font-extrabold rounded-full transition-all duration-300 ease-out shadow-lg hover:shadow-brand-yellow/30 hover:-translate-y-1 hover:scale-105 active:scale-95">
                        {{ __('Browse Products') }}
                        <svg class="w-5 h-5 ml-2 transform transition-transform duration-300 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                       class="group inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-full border border-white/20 transition-all duration-300 ease-out hover:bg-white/20 hover:border-white/40 hover:-translate-y-1 active:scale-95">
                        <svg class="w-5 h-5 mr-2 transform transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ __('Visit Showroom') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Stats bar ────────────────────────────────────────── --}}
    <section class="bg-brand-red text-white py-8" aria-label="Statistics">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div data-aos="fade-up" data-aos-delay="0">
                    <div class="text-3xl font-black tabular-nums">
                        <span data-count="500" data-suffix="+">500+</span>
                    </div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Product options') }}</div>
                </div>
                <div data-aos="fade-up" data-aos-delay="100">
                    <div class="text-3xl font-black tabular-nums">
                        <span data-count="10000" data-suffix="+" data-display-format="short">10K+</span>
                    </div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Customers served') }}</div>
                </div>
                <div data-aos="fade-up" data-aos-delay="200">
                    <div class="text-3xl font-black">1:1</div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Showroom guidance') }}</div>
                </div>
                <div data-aos="fade-up" data-aos-delay="300">
                    <div class="text-3xl font-black tabular-nums">
                        <span data-count="10" data-suffix="+">10+</span>
                    </div>
                    <div class="text-sm opacity-80 mt-1">{{ __('Years Experience') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Browse by Category ───────────────────────────────── --}}
    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="categories-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 id="categories-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('Browse by Category') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Explore what is available before you visit or enquire') }}</p>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $i => $category)
                <a href="{{ route('products', ['category' => $category->id]) }}"
                   data-aos="zoom-in" data-aos-delay="{{ $i * 60 }}"
                   class="group bg-gray-50 dark:bg-gray-700 border-2 border-transparent hover:border-brand-red rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-red group-hover:text-white transition-all duration-300 group-hover:scale-110 text-2xl" aria-hidden="true">
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

    {{-- ── Featured Products ────────────────────────────────── --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="featured-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12 gap-4" data-aos="fade-up">
                <div>
                    <h2 id="featured-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('Featured Products') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Popular products customers often ask about in store') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm shrink-0">
                    {{ __('See Full Showcase') }}
                </a>
            </div>

            @if($featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredProducts as $i => $product)
                <a href="{{ route('product.show', $product->slug) }}"
                   data-aos="fade-up" data-aos-delay="{{ $i * 80 }}"
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 hover:-translate-y-1">
                    <div class="relative bg-gray-100 dark:bg-gray-700 h-52 overflow-hidden">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-6xl group-hover:scale-110 transition-transform duration-300" aria-hidden="true">🚗</div>
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

    {{-- ── Why Choose ───────────────────────────────────────── --}}
    <section class="py-16 bg-brand-black text-white" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
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
                ] as $i => [$icon, $title, $description])
                <div data-aos="fade-up" data-aos-delay="{{ $i * 100 }}"
                     class="text-center p-6 bg-gray-800 rounded-2xl hover:bg-gray-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="text-5xl mb-4 transition-transform duration-300 group-hover:scale-110" aria-hidden="true">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Latest Highlights ────────────────────────────────── --}}
    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="arrivals-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-12 gap-4" data-aos="fade-up">
                <div>
                    <h2 id="arrivals-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('Latest Highlights') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Newly added products worth checking before your visit') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm shrink-0">
                    {{ __('Browse Products') }}
                </a>
            </div>

            @if($newArrivals->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($newArrivals as $i => $product)
                <a href="{{ route('product.show', $product->slug) }}"
                   data-aos="zoom-in" data-aos-delay="{{ $i * 80 }}"
                   class="group bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="bg-gray-100 dark:bg-gray-600 h-40 flex items-center justify-center overflow-hidden">
                        @if($product->image)
                        <img src="{{ Storage::url($product->image) }}"
                             alt="{{ $product->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <span class="text-5xl group-hover:scale-110 transition-transform duration-300" aria-hidden="true">🚗</span>
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

    {{-- ── Testimonials ─────────────────────────────────────── --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 id="testimonials-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('What Our Customers Say') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Real feedback about the showroom and consultation experience') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $i => $testimonial)
                <article data-aos="fade-up" data-aos-delay="{{ $i * 100 }}"
                         class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300">
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

    {{-- ── CTA ──────────────────────────────────────────────── --}}
    <section class="py-16 bg-brand-red text-white" aria-labelledby="cta-heading">
        <div class="max-w-4xl mx-auto px-4 text-center" data-aos="zoom-in">
            <h2 id="cta-heading" class="text-3xl sm:text-4xl font-black mb-4">
                {{ __('Ready to plan your visit?') }}
            </h2>
            <p class="text-lg opacity-90 mb-8">
                {{ __('Use the website to explore our products, then visit the showroom or message us on WhatsApp for faster assistance.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-block bg-brand-yellow text-brand-black px-10 py-4 rounded-full font-black text-lg hover:bg-yellow-300 hover:-translate-y-1 transition-all duration-300 shadow-lg">
                    {{ __('Open WhatsApp') }}
                </a>
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-block border-2 border-white px-10 py-4 rounded-full font-black text-lg hover:bg-white hover:text-brand-red hover:-translate-y-1 transition-all duration-300">
                    {{ __('Get Store Directions') }}
                </a>
            </div>
        </div>
    </section>
</div>
