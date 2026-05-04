<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress  = config('services.store.address');
        $whatsAppUrl   = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to learn more about your products.');
        $mapUrl        = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    {{-- ── 1. HERO ── --}}
    <section class="hero-gradient text-white py-20 sm:py-28 relative overflow-hidden" aria-label="{{ __('Hero') }}">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-brand-yellow/10 rounded-full blur-3xl orb-float"></div>
            <div class="absolute -bottom-20 -right-20 w-[36rem] h-[36rem] bg-brand-red/10 rounded-full blur-3xl orb-float-alt"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="max-w-3xl">
                <div class="hero-reveal inline-flex items-center gap-2 bg-brand-yellow/90 backdrop-blur-sm text-brand-black text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-6 shadow-md">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ __('Shah Alam Car Audio & Accessories') }}
                </div>

                <h1 class="hero-reveal-delay1 text-5xl sm:text-6xl md:text-7xl font-black leading-[1.05] mb-6 drop-shadow-sm">
                    {{ __('Your car audio') }}<br>
                    <span class="text-brand-yellow">{{ __('& accessories') }}</span><br>
                    {{ __('specialist.') }}
                </h1>

                <p class="hero-reveal-delay2 text-lg sm:text-xl text-white/80 mb-10 leading-relaxed max-w-xl">
                    {{ __('Discover car audio and accessories online — then visit our showroom in Shah Alam for expert advice and professional installation.') }}
                </p>

                <div class="hero-reveal-delay3 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('products') }}"
                       class="group relative inline-flex items-center justify-center px-8 py-4 bg-brand-yellow text-brand-black font-extrabold rounded-full overflow-hidden shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgba(250,204,21,0.4)] active:scale-95">
                        <span class="absolute inset-0 bg-white/30 -skew-x-12 -translate-x-full group-hover:translate-x-[200%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                        <span class="relative flex items-center gap-2">
                            {{ __('Browse Products') }}
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </a>
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                       class="group inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-md text-white font-bold rounded-full border border-white/25 transition-all duration-300 hover:bg-white/20 hover:border-white/50 hover:-translate-y-1 active:scale-95">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ __('Visit Showroom') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── 2. 3D SHOWCASE ── --}}
    <section class="py-24 bg-white dark:bg-gray-800" aria-labelledby="showcase-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right" class="order-2 lg:order-1">
                    <span class="inline-block text-xs font-black uppercase tracking-[0.2em] text-brand-red bg-brand-red/10 px-3 py-1.5 rounded-full mb-5">
                        {{ __('Interactive 3D') }}
                    </span>
                    <h2 id="showcase-heading" class="text-4xl sm:text-5xl font-black text-brand-black dark:text-white mb-6 leading-tight">
                        {{ __('Experience Our Signature Product in 3D') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed mb-10 max-w-md">
                        {{ __('Rotate, zoom, and explore our featured product before you visit. The 3D viewer lets you inspect every detail from home.') }}
                    </p>
                    @if($showcaseProduct)
                    <a href="{{ route('product.show', $showcaseProduct->slug) }}"
                       class="group inline-flex items-center gap-3 bg-brand-red text-white px-8 py-4 rounded-full font-bold transition-all duration-300 hover:bg-red-700 hover:-translate-y-1 hover:shadow-xl active:scale-95">
                        {{ __('View Product Details') }}
                        <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    @endif
                </div>

                <div data-aos="fade-left" class="order-1 lg:order-2">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-tr from-brand-red/20 to-brand-yellow/20 rounded-[2.5rem] blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        <section id="3d-showcase" class="relative rounded-[2rem] overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]">
                            <div id="3d-mount-homepage"
                                 data-product-slug="{{ $showcaseProduct?->slug ?? 'skynavi-android-player' }}"
                                 class="min-h-[420px] flex items-center justify-center">
                                @if($showcaseProduct?->getImageUrl('card'))
                                    <img src="{{ $showcaseProduct->getImageUrl('card') }}"
                                         alt="{{ $showcaseProduct->name }}"
                                         class="w-full h-[420px] object-cover">
                                @else
                                    <div class="text-center px-8 py-12">
                                        <div class="w-20 h-20 bg-brand-red/10 dark:bg-brand-red/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
                                            <svg class="w-10 h-10 text-brand-red" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                                        </div>
                                        <div class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-2">{{ $showcaseProduct?->name ?? __('3D product showcase placeholder') }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Interactive 3D viewer coming soon.') }}</div>
                                    </div>
                                @endif
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── 3. BRAND CAROUSEL ── --}}
    @if($brands->count() > 0)
    <section class="py-16 bg-white dark:bg-brand-black border-y border-gray-100 dark:border-white/5 overflow-hidden" aria-label="{{ __('Brands we carry') }}">
        <div class="max-w-7xl mx-auto px-4 mb-8 text-center" data-aos="fade-up">
            <p class="text-xs font-black uppercase tracking-[0.3em] text-gray-400 dark:text-white/30">{{ __('Official Partners & Brands') }}</p>
        </div>

        <div class="brand-marquee-wrapper relative overflow-hidden" aria-hidden="true" data-aos="fade-up" data-aos-delay="100">
            <div class="absolute left-0 top-0 bottom-0 w-32 bg-gradient-to-r from-white dark:from-brand-black to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l from-white dark:from-brand-black to-transparent z-10 pointer-events-none"></div>

            <div class="brand-track flex w-max items-center">
                @foreach([1,2] as $_)
                    @foreach($brands as $brand)
                    <div class="brand-item flex items-center justify-center min-w-[140px] pr-24">
                        @if($brand->logo)
                            <img src="{{ Storage::url($brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="h-12 w-auto object-contain opacity-40 hover:opacity-100 transition-all duration-500 filter grayscale hover:grayscale-0 hover:scale-110"
                                 loading="lazy">
                        @else
                            <span class="text-gray-300 dark:text-white/20 hover:text-brand-red dark:hover:text-white font-black text-2xl tracking-widest uppercase transition-all duration-300 whitespace-nowrap hover:scale-110 cursor-default">
                                {{ $brand->name }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 4. BROWSE BY CATEGORY ── --}}
    <section class="py-24 bg-gray-50 dark:bg-gray-900" aria-labelledby="categories-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="inline-block text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">{{ __('Shop by type') }}</span>
                <h2 id="categories-heading" class="text-4xl sm:text-5xl font-black text-brand-black dark:text-white mb-4">
                    {{ __('Browse Categories') }}
                </h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto rounded-full"></div>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories as $i => $category)
                <a href="{{ route('products', ['category' => $category->id]) }}"
                   data-aos="fade-up"
                   class="category-card group bg-white dark:bg-gray-800 border-2 border-transparent rounded-3xl p-4 sm:p-6 md:p-8 text-center hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand-red/20 hover:border-brand-red active:scale-95">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-6 group-hover:bg-brand-red group-hover:text-white group-hover:rotate-[10deg] transition-all duration-500 shadow-sm" aria-hidden="true">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    </div>
                    <div class="font-bold text-base text-gray-800 dark:text-gray-100 group-hover:text-brand-red transition-colors">{{ __($category->name) }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-2 font-medium tracking-wide">{{ $category->products_count }} {{ __('ITEMS') }}</div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ── 5. WHY CHOOSE WIN WIN ── --}}
    <section class="py-24 bg-white dark:bg-brand-black" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="inline-block text-xs font-black uppercase tracking-[0.2em] text-brand-red dark:text-brand-yellow mb-3">{{ __('The Experience') }}</span>
                <h2 id="why-heading" class="text-4xl sm:text-5xl font-black text-brand-black dark:text-white mb-4">
                    {{ __('Why Choose') }} <span class="text-brand-red">Win Win</span>?
                </h2>
                <p class="text-gray-500 dark:text-white/50 max-w-md mx-auto text-lg">{{ __('Expertise, Trust, and Professional Installation.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['<svg class="w-10 h-10 text-brand-yellow" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>', __('Curated Selection'), __('We only stock high-quality accessories that we personally test and recommend.')],
                    ['<svg class="w-10 h-10 text-brand-red" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>', __('Visit Showroom'), __('Come see the products in person before making a decision. No guesswork.')],
                    ['<svg class="w-10 h-10 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>', __('Instant Support'), __('Message us on WhatsApp for quick advice or to check stock availability.')],
                    ['<svg class="w-10 h-10 text-gray-400 dark:text-white/60" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>', __('Expert Installation'), __('Professional fitting services ensure your new gear works perfectly and looks great.')],
                ] as $i => [$icon, $title, $description])
                <div data-aos="fade-up" data-aos-delay="{{ $i * 100 }}"
                     class="group p-6 sm:p-8 lg:p-10 bg-gray-50 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 border border-gray-100 dark:border-white/10 rounded-[2.5rem] transition-all duration-500 shadow-sm hover:shadow-2xl hover:-translate-y-2">
                    <div class="mb-4 sm:mb-6 lg:mb-8 transform transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">{!! $icon !!}</div>
                    <h3 class="text-2xl font-bold text-brand-red dark:text-brand-yellow mb-4">{{ $title }}</h3>
                    <p class="text-gray-600 dark:text-white/50 text-base leading-relaxed">{{ $description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── 6. CUSTOMER REVIEWS (TESTIMONIALS) ── --}}
    @if($testimonials->count() > 0)
    <section class="py-24 bg-gray-50 dark:bg-gray-900" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="inline-block text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">{{ __('Testimonials') }}</span>
                <h2 id="testimonials-heading" class="text-4xl sm:text-5xl font-black text-brand-black dark:text-white mb-4">
                    {{ __('What Our Customers Say') }}
                </h2>
                <div class="w-20 h-1.5 bg-brand-red mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $i => $testimonial)
                <article data-aos="fade-up" data-aos-delay="{{ $i * 150 }}"
                         class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-500">
                    <div class="flex text-brand-yellow text-2xl mb-6">
                        {{ str_repeat('★', $testimonial->rating) }}
                    </div>
                    <blockquote class="text-gray-600 dark:text-gray-400 italic mb-8 leading-relaxed text-lg">"{{ $testimonial->message }}"</blockquote>
                    <div class="flex items-center gap-4">
                        @if($testimonial->image)
                            <img src="{{ Storage::url($testimonial->image) }}" alt="{{ $testimonial->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-brand-red/20 shadow-md" loading="lazy">
                        @else
                            <div class="w-12 h-12 bg-brand-red text-white rounded-full flex items-center justify-center font-black text-lg shadow-md">
                                {{ mb_substr($testimonial->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-bold text-gray-800 dark:text-white text-base">{{ $testimonial->name }}</div>
                            <div class="text-sm text-gray-400 dark:text-gray-500">{{ $testimonial->location }}</div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 7. GET IN TOUCH (CTA) ── --}}
    <section class="py-24 bg-brand-red text-white relative overflow-hidden" aria-labelledby="cta-heading">
        <div class="absolute inset-0 opacity-10" aria-hidden="true">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0 L100 100 Z" fill="white" />
            </svg>
        </div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <div data-aos="zoom-in">
                <h2 id="cta-heading" class="text-4xl sm:text-6xl font-black mb-6 leading-tight">
                    {{ __('Ready to upgrade your car?') }}
                </h2>
                <p class="text-xl opacity-90 mb-12 max-w-2xl mx-auto font-medium">
                    {{ __('Use the website to explore our products, then visit our showroom in Shah Alam for expert installation.') }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                   class="group relative inline-flex items-center justify-center bg-brand-yellow text-brand-black px-8 py-4 sm:px-12 sm:py-5 rounded-full font-black text-lg sm:text-xl transition-all duration-300 shadow-[0_10px_40px_rgba(250,204,21,0.3)] overflow-hidden hover:shadow-[0_15px_50px_rgba(250,204,21,0.5)] hover:-translate-y-2 active:scale-95">
                    <span class="absolute inset-0 bg-white/40 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-1000 ease-out"></span>
                    <span class="relative z-10 flex items-center gap-3">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ __('WhatsApp Support') }}
                    </span>
                </a>
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                   class="group inline-flex items-center justify-center border-2 border-white/80 px-8 py-4 sm:px-12 sm:py-5 rounded-full font-black text-lg sm:text-xl hover:bg-white hover:text-brand-red hover:border-white hover:shadow-2xl hover:-translate-y-2 active:scale-95 transition-all duration-500">
                    <svg class="w-6 h-6 mr-3 transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    {{ __('Directions') }}
                </a>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .brand-track {
            will-change: transform;
            backface-visibility: hidden;
            transform-style: preserve-3d;
        }
        @media (prefers-reduced-motion: reduce) {
            .brand-track { animation: none !important; transform: none !important; }
        }
        .category-card {
            transition: transform 0.3s cubic-bezier(0.2, 0, 0.2, 1), border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .category-card:hover {
            border-color: rgb(var(--brand-red-rgb)) !important;
            box-shadow: 0 0 20px rgba(var(--brand-red-rgb), 0.3) !important;
        }
        .category-card:active {
            transform: scale(0.92) !important;
            transition: transform 0.05s linear !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
    (function () {
        function initMarquee() {
            const wrapper = document.querySelector('.brand-marquee-wrapper');
            const track   = document.querySelector('.brand-track');
            if (!wrapper || !track) return;

            const BASE_SPEED = 0.55; // px per frame (~33px/s at 60fps)
            let pos     = 0;
            let speed   = BASE_SPEED;
            let target  = BASE_SPEED;
            let halfW   = 0;
            let raf     = null;

            function measure() { halfW = track.scrollWidth / 2; }
            measure();
            window.addEventListener('resize', measure);

            wrapper.addEventListener('mouseenter', () => { target = 0; });
            wrapper.addEventListener('mouseleave', () => { target = BASE_SPEED; });

            function tick() {
                // Lerp: each frame speed moves 10% closer to target → smooth ease in/out
                speed += (target - speed) * 0.1;
                pos   -= speed;
                if (pos <= -halfW) pos += halfW;
                track.style.transform = 'translate3d(' + pos + 'px, 0, 0)';
                raf = requestAnimationFrame(tick);
            }

            raf = requestAnimationFrame(tick);
        }

        // Run after Livewire hydrates (safe for both first load and navigations)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMarquee);
        } else {
            initMarquee();
        }
        document.addEventListener('livewire:navigated', initMarquee);
    }());
    </script>
    @endpush
</div>
