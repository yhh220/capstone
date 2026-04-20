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
                       class="group relative inline-flex items-center justify-center px-8 py-4 bg-brand-yellow text-brand-black font-extrabold rounded-full transition-all duration-300 ease-out shadow-lg overflow-hidden hover:shadow-[0_0_20px_rgba(232,100,96,0.4)] hover:-translate-y-1 hover:scale-105 active:scale-95">
                        <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                        <span class="relative flex items-center z-10">
                            {{ __('Browse Products') }}
                            <svg class="w-5 h-5 ml-2 transform transition-transform duration-300 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </a>
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                       class="group relative inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-full border border-white/20 transition-all duration-300 ease-out overflow-hidden hover:bg-white/20 hover:border-white/40 hover:-translate-y-1 active:scale-95">
                        <span class="absolute inset-0 w-full h-full bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out"></span>
                        <span class="relative flex items-center z-10">
                            <svg class="w-5 h-5 mr-2 transform transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ __('Visit Showroom') }}
                        </span>
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
                    <div class="w-14 h-14 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-brand-red group-hover:text-white transition-all duration-500 group-hover:scale-110 shadow-sm group-hover:shadow-lg group-hover:-translate-y-1" aria-hidden="true">
                        <svg class="w-7 h-7 group-hover:-translate-y-0.5 group-hover:scale-110 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
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
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-12 gap-2" data-aos="fade-up">
                <div>
                    <h2 id="featured-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-2">
                        {{ __('Featured Products') }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Popular products customers often ask about in store') }}</p>
                </div>
                <a href="{{ route('products') }}" class="text-brand-red font-semibold hover:underline text-sm shrink-0 self-start sm:self-center">
                    {{ __('See Full Showcase') }} →
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
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 transition-all duration-500" aria-hidden="true">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 group-hover:-translate-y-2 group-hover:scale-110 group-hover:drop-shadow-lg transition-all duration-500" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </div>
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
                        {{-- Price (shown when admin enables online shopping) --}}
                        @if($shoppingEnabled)
                        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="text-brand-red font-black text-lg">RM {{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-gray-400 line-through text-sm ml-1">RM {{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="text-brand-red font-black text-lg">RM {{ number_format($product->price, 2) }}</span>
                            @endif
                            @if($product->stock > 0)
                                <span class="text-green-500 text-xs ml-2">{{ __('In Stock') }}</span>
                            @else
                                <span class="text-red-400 text-xs ml-2">{{ __('Out of Stock') }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="showcase-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div data-aos="fade-right">
                    <div class="inline-flex bg-red-50 text-brand-red text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                        {{ __('3D Showcase Ready') }}
                    </div>
                    <h2 id="showcase-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-4">
                        {{ __('Experience Our Signature Product in 3D') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                        {{ __('This section is prepared as the homepage mount point for the team 3D integration. Until the interactive model is attached, customers can still preview the featured product and continue to the detail page.') }}
                    </p>
                    @if($showcaseProduct)
                    <a href="{{ route('product.show', $showcaseProduct->slug) }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-brand-red text-white rounded-full font-bold hover:bg-red-700 transition-colors">
                        {{ __('View Product Details') }}
                    </a>
                    @endif
                </div>

                <div data-aos="fade-left">
                    <section id="3d-showcase" class="rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                        <div id="3d-mount-homepage"
                             data-product-slug="{{ $showcaseProduct?->slug ?? 'skynavi-android-player' }}"
                             class="min-h-[360px] flex items-center justify-center">
                            @if($showcaseProduct?->getImageUrl('card'))
                            <img src="{{ $showcaseProduct->getImageUrl('card') }}"
                                 alt="{{ $showcaseProduct->name }}"
                                 class="w-full h-[360px] object-cover">
                            @else
                            <div class="text-center px-6">
                                <div class="text-6xl mb-4">3D</div>
                                <div class="font-bold text-gray-800 dark:text-gray-100">{{ $showcaseProduct?->name ?? __('3D product showcase placeholder') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Soon Gor can mount the interactive viewer into this container.') }}</div>
                            </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Why Choose ───────────────────────────────────────── --}}
    <section class="py-16 bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 id="why-heading" class="text-3xl sm:text-4xl font-black mb-3">
                    {{ __('Why Choose') }} <span class="text-brand-red">Win Win</span>?
                </h2>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Built to create trust before the customer even steps into the store') }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['<svg class="w-10 h-10 group-hover:scale-125 group-hover:-translate-y-2 transition-all duration-500 drop-shadow-md text-brand-yellow" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>', __('Curated products'), __('We highlight proven accessories customers regularly ask for.')],
                    ['<svg class="w-10 h-10 group-hover:scale-125 group-hover:-translate-y-2 transition-all duration-500 drop-shadow-md text-brand-red" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>', __('Real showroom'), __('Customers can inspect products and discuss options in person.')],
                    ['<svg class="w-10 h-10 group-hover:scale-125 group-hover:-translate-y-2 transition-all duration-500 drop-shadow-md text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>', __('WhatsApp support'), __('Ask questions quickly before making the trip to the store.')],
                    ['<svg class="w-10 h-10 group-hover:scale-125 group-hover:rotate-45 group-hover:-translate-y-2 transition-all duration-500 drop-shadow-md text-gray-800 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>', __('Installation advice'), __('Our team helps match products to your vehicle and needs.')],
                ] as $i => [$icon, $title, $description])
                <div data-aos="fade-up" data-aos-delay="{{ $i * 100 }}"
                     class="group text-center p-6 bg-white dark:bg-gray-800 rounded-2xl border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-500 shadow-sm hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-3">
                    <div class="flex justify-center mb-6" aria-hidden="true">{!! $icon !!}</div>
                    <h3 class="text-xl font-bold text-brand-red mb-2">{{ $title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $description }}</p>
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
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 transition-all duration-500" aria-hidden="true">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-500 group-hover:rotate-[25deg] group-hover:scale-125 group-hover:text-brand-yellow group-hover:-translate-y-2 transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path><path d="M5 3v4"></path><path d="M19 17v4"></path><path d="M3 5h4"></path><path d="M17 19h4"></path></svg>
                        </div>
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="font-semibold text-sm text-gray-800 dark:text-gray-200 group-hover:text-brand-red transition-colors line-clamp-2">
                            {{ $product->name }}
                        </div>
                        @if($shoppingEnabled)
                        <div class="mt-1">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="text-brand-red font-bold text-sm">RM {{ number_format($product->sale_price, 2) }}</span>
                            @else
                                <span class="text-brand-red font-bold text-sm">RM {{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        @else
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Tap to view details and enquire') }}</div>
                        @endif
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
                   class="group relative inline-flex items-center justify-center bg-brand-yellow text-brand-black px-10 py-4 rounded-full font-black text-lg transition-all duration-300 shadow-lg overflow-hidden hover:shadow-[0_0_20px_rgba(232,100,96,0.4)] hover:-translate-y-1 active:scale-95">
                    <span class="absolute inset-0 w-full h-full bg-white/30 skew-x-[45deg] -translate-x-[150%] group-hover:translate-x-[150%] transition-transform duration-700 ease-out"></span>
                    <span class="relative z-10 flex items-center">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-[15deg] transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ __('Open WhatsApp') }}
                    </span>
                </a>
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                   class="group inline-flex items-center justify-center border-2 border-white px-10 py-4 rounded-full font-black text-lg hover:bg-white hover:text-brand-red hover:shadow-[0_4px_20px_rgba(255,255,255,0.3)] hover:-translate-y-1 active:scale-95 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    {{ __('Get Store Directions') }}
                </a>
            </div>
        </div>
    </section>
</div>
