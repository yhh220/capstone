<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress  = config('services.store.address');
        $whatsAppUrl   = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to learn more about your products.');
        $mapUrl        = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);

        // Lucide icons (lucide.dev) — stroke style, inherit currentColor
        $lucide = [
            'arrow-right'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/><path stroke-linecap="round" stroke-linejoin="round" d="m12 5 7 7-7 7"/>',
            'map-pin'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
            'wrench'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
            'badge-check'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>',
            'message-circle' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
            'rotate-3d'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.466 7.5C15.643 4.237 13.952 2 12 2 9.239 2 7 6.477 7 12s2.239 10 5 10c.342 0 .677-.069 1-.2"/><path stroke-linecap="round" stroke-linejoin="round" d="m15.194 13.707 3.814 1.86-1.86 3.814"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 15.57c-1.804.885-4.274 1.43-7 1.43-5.523 0-10-2.239-10-5s4.477-5 10-5c4.838 0 8.873 1.718 9.8 4"/>',
            'zoom-in'        => '<circle cx="11" cy="11" r="8"/><line stroke-linecap="round" x1="21" x2="16.65" y1="21" y2="16.65"/><line stroke-linecap="round" x1="11" x2="11" y1="8" y2="14"/><line stroke-linecap="round" x1="8" x2="14" y1="11" y2="11"/>',
            'smartphone'     => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path stroke-linecap="round" d="M12 18h.01"/>',
            'layout-grid'    => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
        ];
        $icon = fn (string $name, string $class = 'w-5 h-5') =>
            '<svg class="' . $class . '" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">' . $lucide[$name] . '</svg>';

        // WhatsApp brand glyph (svgl.app)
        $waGlyph = '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>';
    @endphp

    {{-- ── 1. HERO ── --}}
    <section class="relative overflow-hidden bg-white dark:bg-[#0C0C0E]" aria-label="{{ __('Hero') }}">
        {{-- Background video — the footage is dark with warm tones, which turns
             muddy brown under a white wash. Light mode greyscales + brightens the
             video first so the wash reads as clean silver; dark mode keeps the
             original colours under a dark gradient. --}}
        <div class="absolute inset-0 z-0 pointer-events-none">
            <video autoplay loop muted playsinline
                   class="absolute inset-0 w-full h-full object-cover [filter:grayscale(1)_brightness(1.05)_contrast(1.05)] dark:[filter:none]">
                <source src="{{ asset('images/videos/hero-bg.mp4') }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-white/20 dark:bg-[#0C0C0E]/55"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/45 to-transparent dark:from-[#0C0C0E]/90 dark:via-[#0C0C0E]/40 dark:to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20 lg:py-32">
            <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-20">

                {{-- Text Content --}}
                <div class="flex-1 min-w-0 hero-enter text-center lg:text-left">
                    <p class="font-mono text-[11px] tracking-[0.25em] uppercase text-gray-400 dark:text-white/35 mb-6 sm:mb-8">
                        {{ __('Shah Alam, Selangor') }} &nbsp;·&nbsp; {{ __('Malaysia') }}
                    </p>
                    <h1 class="font-display text-[clamp(2.8rem,9vw,6.8rem)] leading-[0.93] uppercase text-gray-900 dark:text-white mb-6 sm:mb-8">
                        {{ __("Shah Alam's") }}<br>
                        <span class="text-[#C8413D]">{{ __('Car Audio') }}</span><br>
                        {{ __('Specialist.') }}
                    </h1>
                    <p class="text-gray-500 dark:text-white/45 text-base sm:text-lg mb-8 sm:mb-10 max-w-md mx-auto lg:mx-0 leading-relaxed">
                        {{ __('Come in, browse the products, take a look around. Expert installation in Shah Alam.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center lg:items-start justify-center lg:justify-start gap-4">
                        @if($shoppingEnabled)
                        <a href="{{ route('products') }}"
                           class="group relative inline-flex items-center justify-center gap-3 bg-[#C8413D] text-white px-8 py-4 rounded-xl font-black text-lg transition-all duration-300 shadow-[0_6px_20px_rgba(200,65,61,0.35)] overflow-hidden hover:shadow-[0_10px_30px_rgba(200,65,61,0.5)] hover:-translate-y-1 active:scale-95 w-full sm:w-auto">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <span class="relative z-10">{{ __('Browse Products') }}</span>
                            <i data-lucide="arrow-right" class="icon-sm relative z-10 transition-transform duration-300 group-hover:translate-x-1"></i>
                        </a>
                        @endif
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                           class="group inline-flex items-center justify-center gap-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-8 py-4 rounded-xl font-black text-lg hover:border-[#C8413D] hover:text-[#C8413D] hover:bg-gray-50 dark:hover:bg-gray-800 hover:-translate-y-1 hover:shadow-md transition-all duration-300 active:scale-95 w-full sm:w-auto">
                            <i data-lucide="map-pin" class="icon-sm"></i>
                            {{ __('Visit Showroom') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gray-200 dark:bg-transparent" aria-hidden="true"></div>
    </section>

    {{-- ── 2. BROWSE BY CATEGORY ── (show what you sell, right after the hero) --}}
    <section class="py-16 sm:py-20" aria-labelledby="categories-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-10" data-aos="fade-up">
                <div>
                    <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                        <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                        {{ __('Shop by type') }}
                    </span>
                    <h2 id="categories-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white">
                        {{ __('Browse Categories') }}
                    </h2>
                </div>
                <a href="{{ route('products') }}"
                   class="group inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-brand-red transition-colors">
                    {{ __('View all products') }}
                    {!! $icon('arrow-right', 'w-4 h-4 transition-transform duration-300 group-hover:translate-x-1') !!}
                </a>
            </div>

            @if($categories->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $i => $category)
                <a href="{{ route('products', ['category' => $category->id]) }}"
                   data-aos="fade-up" data-aos-delay="{{ $i * 50 }}"
                   class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 transition-all duration-300 hover:border-brand-red/50 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98]">
                    <div class="w-10 h-10 rounded-xl bg-brand-red/10 text-brand-red flex items-center justify-center mb-4 transition-colors duration-300 group-hover:bg-brand-red group-hover:text-white" aria-hidden="true">
                        {!! $icon('layout-grid', 'w-5 h-5') !!}
                    </div>
                    <div class="font-bold text-sm text-gray-800 dark:text-gray-100 leading-snug group-hover:text-brand-red transition-colors">{{ __($category->name) }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1 font-medium">{{ $category->products_count }} {{ __('ITEMS') }}</div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ── 3. FEATURED PRODUCTS ── (real items hook the visitor — driven by the "Show on Homepage" toggle) --}}
    @if($featuredProducts->count() > 0)
    <section class="py-16 sm:py-20 bg-gray-50 dark:bg-gray-900/40" aria-labelledby="featured-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-10" data-aos="fade-up">
                <div>
                    <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                        <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                        {{ __('Handpicked') }}
                    </span>
                    <h2 id="featured-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white">
                        {{ __('Featured Products') }}
                    </h2>
                </div>
                <a href="{{ route('products') }}"
                   class="group inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-brand-red transition-colors">
                    {{ __('View all products') }}
                    {!! $icon('arrow-right', 'w-4 h-4 transition-transform duration-300 group-hover:translate-x-1') !!}
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($featuredProducts as $i => $product)
                <a href="{{ route('product.show', $product->slug) }}"
                   data-aos="fade-up" data-aos-delay="{{ $i % 4 * 60 }}"
                   class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden transition-all duration-300 hover:border-brand-red/50 hover:shadow-md hover:-translate-y-0.5 flex flex-col">
                    <div class="relative aspect-square bg-gray-100 dark:bg-gray-900 overflow-hidden">
                        @if($product->getImageUrl('thumb'))
                            <img src="{{ $product->getImageUrl('thumb') }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600" aria-hidden="true">
                                {!! $icon('layout-grid', 'w-10 h-10') !!}
                            </div>
                        @endif
                        @if($shoppingEnabled && $product->sale_price && $product->sale_price < $product->price)
                        <span class="absolute top-3 left-3 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded-full">{{ __('SALE') }}</span>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ $product->category?->name }}</div>
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100 leading-snug line-clamp-2 group-hover:text-brand-red transition-colors">{{ $product->name }}</h3>
                        @if($shoppingEnabled)
                        <div class="mt-auto pt-3 flex items-center gap-2">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="text-brand-red font-black">RM {{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-gray-400 line-through text-xs">RM {{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="text-brand-red font-black">RM {{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        @else
                        <div class="mt-auto pt-3">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-brand-red group-hover:gap-2 transition-all">
                                {{ __('View Details') }}
                                {!! $icon('arrow-right', 'w-3.5 h-3.5') !!}
                            </span>
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 4. WHY CHOOSE WIN WIN ── (value props build trust right after the products) --}}
    <section class="py-16 sm:py-20" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-10" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                    <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                    {{ __('The Experience') }}
                </span>
                <h2 id="why-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white mb-3">
                    {{ __('Why Choose') }} <span class="text-brand-red">Win Win</span>?
                </h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-md">{{ __('Expertise, Trust, and Professional Installation.') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    [$icon('badge-check'), __('Curated Selection'), __('We only stock high-quality accessories that we personally test and recommend.')],
                    [$icon('map-pin'), __('Visit Showroom'), __('Come see the products in person before making a decision. No guesswork.')],
                    ['<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">' . $waGlyph . '</svg>', __('Instant Support'), __('Message us on WhatsApp for quick advice or to check stock availability.')],
                    [$icon('wrench'), __('Expert Installation'), __('Professional fitting services ensure your new gear works perfectly and looks great.')],
                ] as $i => [$cardIcon, $title, $description])
                <div data-aos="fade-up" data-aos-delay="{{ $i * 75 }}"
                     class="group p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl transition-all duration-300 hover:border-brand-red/50 hover:shadow-md hover:-translate-y-0.5">
                    <div class="w-11 h-11 rounded-xl bg-brand-red/10 text-brand-red flex items-center justify-center mb-5 transition-colors duration-300 group-hover:bg-brand-red group-hover:text-white" aria-hidden="true">{!! $cardIcon !!}</div>
                    <h3 class="text-xl text-brand-black dark:text-white mb-2.5">{{ $title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── 5. 3D SHOWCASE ── (interactive differentiator / delight moment) --}}
    <section class="py-16 sm:py-20 bg-gray-50 dark:bg-gray-900/40" aria-labelledby="showcase-heading">
        {{-- Draco decoder must be configured before model-viewer loads; the local
             path keeps it inside our CSP (Google's CDN is not allowlisted). --}}
        <script>self.ModelViewerElement = self.ModelViewerElement || {}; self.ModelViewerElement.dracoDecoderLocation = '{{ asset('draco') }}/';</script>
        <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-[2rem] p-6 sm:p-10 lg:p-14">
                <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                    <div data-aos="fade-right">
                        <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                            <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                            {{ __('Interactive 3D') }}
                        </span>
                        <h2 id="showcase-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white mb-5">
                            {{ __('Explore Your Car in 3D') }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-7 max-w-md">
                            {{ __('Rotate, zoom, and inspect every angle of our 3D car model. See how our accessories fit before you visit.') }}
                        </p>
                        <ul class="space-y-3 text-sm font-semibold text-gray-600 dark:text-gray-400 mb-9">
                            <li class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-brand-red/10 text-brand-red flex items-center justify-center shrink-0">{!! $icon('rotate-3d', 'w-4 h-4') !!}</span>
                                {{ __('Drag to rotate') }}
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-brand-red/10 text-brand-red flex items-center justify-center shrink-0">{!! $icon('zoom-in', 'w-4 h-4') !!}</span>
                                {{ __('Scroll to zoom') }}
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-brand-red/10 text-brand-red flex items-center justify-center shrink-0">{!! $icon('smartphone', 'w-4 h-4') !!}</span>
                                {{ __('Pinch to zoom on mobile') }}
                            </li>
                        </ul>
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-md btn-shine">
                            {{ __('Visit Our Showroom') }}
                            {!! $icon('arrow-right', 'icon-md') !!}
                        </a>
                    </div>

                    <div data-aos="fade-left">
                        <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-900">
                            <model-viewer
                                src="{{ asset('models/3d/city-draco.glb') }}"
                                alt="{{ __('3D car model — Win Win Car Studio') }}"
                                auto-rotate
                                auto-rotate-delay="1000"
                                rotation-per-second="20deg"
                                camera-controls
                                touch-action="pan-y"
                                shadow-intensity="1"
                                exposure="0.8"
                                style="width:100%; height:440px; background:transparent;"
                                loading="lazy">
                                <div slot="progress-bar" style="display:none;"></div>
                                <div slot="poster"
                                     style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:#111827;">
                                    <svg style="width:48px;height:48px;color:#C8413D;animation:spin3d 1s linear infinite;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                                    </svg>
                                </div>
                            </model-viewer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── 6. SOCIAL PROOF ── (testimonials + partner brands together build credibility) --}}
    @if($testimonials->count() > 0)
    <section class="py-16 sm:py-20" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-10" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                    <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                    {{ __('Testimonials') }}
                </span>
                <h2 id="testimonials-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white">
                    {{ __('What Our Customers Say') }}
                </h2>
            </div>

            {{-- Pull quote: first testimonial --}}
            <div class="mb-10 max-w-3xl" data-aos="fade-up">
                <p class="font-display text-[clamp(1.4rem,3.5vw,2.4rem)] leading-tight text-gray-900 dark:text-white mb-5 uppercase">
                    "{{ $testimonials->first()->message }}"
                </p>
                <div class="flex items-center gap-3">
                    @if($testimonials->first()->image)
                        <img src="{{ Storage::url($testimonials->first()->image) }}"
                             alt="{{ $testimonials->first()->name }}"
                             class="w-10 h-10 rounded-full object-cover"
                             loading="lazy">
                    @else
                        <div class="w-10 h-10 bg-brand-red/10 text-brand-red rounded-full flex items-center justify-center font-black text-sm shrink-0">
                            {{ mb_substr($testimonials->first()->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $testimonials->first()->name }}</span>
                        @if($testimonials->first()->location)
                        <span class="text-sm text-gray-400 dark:text-gray-500"> · {{ $testimonials->first()->location }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Scroll row: remaining testimonials --}}
            @if($testimonials->count() > 1)
            <div class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 snap-x snap-mandatory scrollbar-hide" data-aos="fade-up">
                @foreach($testimonials->skip(1) as $testimonial)
                <article class="snap-start shrink-0 w-72 sm:w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 transition-all duration-300 hover:border-brand-red/40 hover:shadow-md flex flex-col">
                    <div class="flex text-amber-400 gap-0.5 mb-4" role="img" aria-label="{{ $testimonial->rating }}/5">
                        @for($s = 0; $s < $testimonial->rating; $s++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed mb-5 flex-1">
                        "{{ Str::limit($testimonial->message, 120) }}"
                    </blockquote>
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                        @if($testimonial->image)
                            <img src="{{ Storage::url($testimonial->image) }}"
                                 alt="{{ $testimonial->name }}"
                                 class="w-8 h-8 rounded-full object-cover"
                                 loading="lazy">
                        @else
                            <div class="w-8 h-8 bg-brand-red/10 text-brand-red rounded-full flex items-center justify-center font-bold text-xs shrink-0">
                                {{ mb_substr($testimonial->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $testimonial->name }}</div>
                            @if($testimonial->location)
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $testimonial->location }}</div>
                            @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ── 6b. PARTNER BRANDS ── (credibility cue, sits with the social proof) --}}
    @if($brands->count() > 0)
    <section class="py-12 border-t border-gray-200 dark:border-gray-700/60 overflow-hidden" aria-label="{{ __('Brands we carry') }}">
        <div class="max-w-7xl mx-auto px-4 mb-7">
            <p class="text-xs font-black uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500">{{ __('Official Partners & Brands') }}</p>
        </div>

        <div class="brand-marquee-wrapper relative overflow-hidden" aria-hidden="true">
            <div class="absolute left-0 top-0 bottom-0 w-32 bg-gradient-to-r from-[rgb(var(--app-bg-rgb))] to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-32 bg-gradient-to-l from-[rgb(var(--app-bg-rgb))] to-transparent z-10 pointer-events-none"></div>

            {{-- Three copies so the track always fills the viewport during the
                 wrap (two left a sliver of empty space on wide screens). --}}
            <div class="brand-track flex w-max items-center">
                @foreach([1,2,3] as $_)
                    @foreach($brands as $brand)
                    {{-- Clickable (opens the brand's site) when a website URL is set,
                         otherwise a plain block. The marquee is decorative
                         (aria-hidden), so links stay out of the tab order. --}}
                    @php $tag = $brand->website_url ? 'a' : 'div'; @endphp
                    <{{ $tag }} class="brand-item flex items-center justify-center min-w-[140px] pr-24 {{ $brand->website_url ? 'cursor-pointer' : '' }}"
                        @if($brand->website_url) href="{{ $brand->website_url }}" target="_blank" rel="noopener noreferrer" tabindex="-1" @endif>
                        @if($brand->display_type === 'image' && $brand->logo)
                            <img src="{{ Storage::url($brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="h-10 w-auto object-contain"
                                 loading="lazy">
                        @else
                            <span class="text-gray-600 dark:text-gray-300 font-black text-xl tracking-widest uppercase whitespace-nowrap">
                                {{ $brand->name }}
                            </span>
                        @endif
                    </{{ $tag }}>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 7. GET IN TOUCH (CTA) ── --}}
    <section class="py-16 sm:py-20" aria-labelledby="cta-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="relative overflow-hidden rounded-[2rem] bg-[#121212] dark:bg-[#1C1917] border border-gray-800 dark:border-gray-700 px-6 py-14 sm:px-14 sm:py-16" data-aos="fade-up">
                {{-- Contained accent glow --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-red/25 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
                <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-brand-red/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

                <div class="relative grid lg:grid-cols-[1.2fr_auto] gap-10 items-center">
                    <div>
                        <h2 id="cta-heading" class="text-3xl sm:text-5xl text-white mb-4 leading-tight">
                            {{ __('Ready to upgrade your car?') }}
                        </h2>
                        <p class="text-white/70 text-base sm:text-lg max-w-xl">
                            {{ __('Use the website to explore our products, then visit our showroom in Shah Alam for expert installation.') }}
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 shrink-0">
                        <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                           class="group inline-flex items-center justify-center gap-2.5 bg-[#25D366] text-white px-7 py-3.5 rounded-full font-extrabold text-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(37,211,102,0.35)] active:scale-95">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $waGlyph !!}</svg>
                            {{ __('WhatsApp Support') }}
                        </a>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                           class="group inline-flex items-center justify-center gap-2.5 border border-white/25 text-white px-7 py-3.5 rounded-full font-extrabold text-sm transition-all duration-300 hover:bg-white/10 hover:border-white/50 hover:-translate-y-0.5 active:scale-95">
                            {!! $icon('map-pin', 'w-5 h-5') !!}
                            {{ __('Directions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        /* Hero entrance */
        .hero-enter { animation: hp-in 0.55s ease both; }
        .hero-enter-delay { animation: hp-in 0.55s 0.12s ease both; }
        @keyframes hp-in {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* 3D poster spinner */
        @keyframes spin3d { to { transform: rotate(360deg); } }

        /* Brand marquee */
        .brand-track {
            will-change: transform;
            backface-visibility: hidden;
        }
        @media (prefers-reduced-motion: reduce) {
            .brand-track { animation: none !important; transform: none !important; }
            .hero-enter, .hero-enter-delay { animation: none; opacity: 1; transform: none; }
        }

        /* Hide scrollbar for testimonials row */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush

    @push('scripts')
    <script>
    (function () {
        // Hoisted so each (re)init can cancel the previous loop. Livewire
        // navigations re-run this script; without cancelling, every visit to
        // the homepage stacked another rAF loop (all mutating the same
        // transform → speed-up/jank) and the loop kept running against a
        // detached node after navigating away.
        let rafId = null;
        let measureFn = null;
        let listenersBound = false;

        function initMarquee() {
            const wrapper = document.querySelector('.brand-marquee-wrapper');
            const track   = document.querySelector('.brand-track');
            if (!wrapper || !track) return;
            // Respect prefers-reduced-motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            // Kill any loop left over from a previous page before starting one.
            if (rafId !== null) { cancelAnimationFrame(rafId); rafId = null; }

            const BASE_SPEED = 0.22; // px per frame (~13px/s at 60fps) — slow, calm scroll
            let pos = 0, speed = BASE_SPEED, target = BASE_SPEED, halfW = 0;
            // Measure the width of one copy and (re)start one copy to the left so
            // the rightward scroll always has content to reveal. Logos are images
            // that load after first paint and change the track width, so we
            // re-measure on load/resize — measuring too early left a gap on the
            // right and a wrong wrap point.
            function measure() { halfW = track.scrollWidth / 3; pos = -halfW; }
            measure();

            // Bind window listeners only once; they call whatever the current
            // measure() is, so repeated inits don't pile up duplicate handlers.
            measureFn = measure;
            if (!listenersBound) {
                listenersBound = true;
                window.addEventListener('load',   () => { if (measureFn) measureFn(); });
                window.addEventListener('resize', () => { if (measureFn) measureFn(); });
            }

            track.querySelectorAll('img').forEach((img) => {
                if (!img.complete) img.addEventListener('load', measure, { once: true });
            });
            wrapper.addEventListener('mouseenter', () => { target = 0; });
            wrapper.addEventListener('mouseleave', () => { target = BASE_SPEED; });

            function tick() {
                // Stop once the track is gone (navigated away) instead of
                // burning frames forever on a detached element.
                if (!track.isConnected) { rafId = null; return; }
                speed += (target - speed) * 0.1;
                pos   += speed;                          // move the track right
                if (halfW > 0 && pos >= 0) pos -= halfW; // seamless wrap (two identical copies)
                track.style.transform = 'translate3d(' + pos + 'px, 0, 0)';
                rafId = requestAnimationFrame(tick);
            }
            rafId = requestAnimationFrame(tick);
        }
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
