<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress  = config('services.store.address');
        $whatsAppUrl   = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to learn more about your products.');
        $mapUrl        = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    {{-- ── 1. HERO ── --}}
    <section class="bg-white dark:bg-[#0C0C0E] relative overflow-hidden" aria-label="{{ __('Hero') }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20 lg:py-32">
            <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-20">

                {{-- Left: Text --}}
                <div class="flex-1 min-w-0 hero-enter text-center lg:text-left">
                    <p class="font-mono text-[11px] tracking-[0.25em] uppercase text-gray-400 dark:text-white/35 mb-6 sm:mb-8">
                        Shah Alam, Selangor &nbsp;·&nbsp; Malaysia
                    </p>
                    <h1 class="font-display text-[clamp(2.8rem,9vw,6.8rem)] leading-[0.93] uppercase text-gray-900 dark:text-white mb-6 sm:mb-8">
                        SHAH ALAM'S<br>
                        <span class="text-[#C8413D]">CAR AUDIO</span><br>
                        SPECIALIST.
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

                {{-- Right: Image — hidden on mobile, visible md+ --}}
                <div class="hidden md:block flex-shrink-0 w-full lg:w-[44%] xl:w-[460px] hero-enter-delay">
                    <div class="relative">
                        <div class="absolute -left-3 top-6 bottom-6 w-px bg-[#C8413D] opacity-35" aria-hidden="true"></div>
                        <img src="{{ asset('images/storefront.png') }}"
                             alt="{{ __('Win Win Car Audio showroom in Shah Alam') }}"
                             class="w-full h-auto object-cover"
                             loading="eager" width="480" height="534">
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gray-200 dark:bg-transparent" aria-hidden="true"></div>
    </section>

    {{-- ── 2. BRAND PARTNERS ── --}}
    @if($brands->count() > 0)
    <section class="py-10 sm:py-14 overflow-hidden border-b border-gray-100 dark:border-white/5" aria-label="{{ __('Brands we carry') }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mb-6 sm:mb-8">
            <p class="font-mono text-[10px] tracking-[0.3em] uppercase text-gray-400 dark:text-white/25">{{ __('Our Brand Partners') }}</p>
        </div>
        <div class="brand-marquee-wrapper relative overflow-hidden" aria-hidden="true">
            <div class="brand-track flex w-max items-center">
                @foreach([1,2] as $_)
                    @foreach($brands as $brand)
                    <div class="brand-item flex items-center justify-center min-w-[120px] sm:min-w-[140px] pr-16 sm:pr-24">
                        @if($brand->logo)
                            <img src="{{ Storage::url($brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="h-8 sm:h-10 w-auto object-contain opacity-30 hover:opacity-100 transition-all duration-500 filter grayscale hover:grayscale-0"
                                 loading="lazy">
                        @else
                            <span class="text-gray-300 dark:text-white/20 hover:text-[#C8413D] font-black text-lg sm:text-xl tracking-widest uppercase transition-all duration-300 whitespace-nowrap cursor-default">
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

    {{-- ── 3. CATEGORIES ── --}}
    @if($categories->count() > 0)
    <section class="py-14 sm:py-20 bg-white dark:bg-[#0C0C0E]" aria-labelledby="categories-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex items-end justify-between mb-5">
                <p class="font-mono text-[10px] tracking-[0.3em] uppercase text-gray-400 dark:text-white/30" id="categories-heading">{{ __('Shop by Category') }}</p>
                @if($shoppingEnabled)
                <a href="{{ route('products') }}" class="text-xs font-medium tracking-wide hover:underline" style="color:#C8413D;">{{ __('All Products') }} →</a>
                @endif
            </div>
            <div class="border-t border-gray-200 dark:border-white/10">
                @foreach($categories as $category)
                <a href="{{ route('products', ['category' => $category->id]) }}"
                   class="cat-row group flex items-center justify-between py-4 sm:py-5 border-b border-gray-200 dark:border-white/10 px-3 sm:px-4 -mx-3 sm:-mx-4 transition-colors duration-200">
                    <span class="font-display text-lg sm:text-xl font-black uppercase text-gray-900 dark:text-white group-hover:text-white transition-colors">{{ __($category->name) }}</span>
                    <div class="flex items-center gap-3 sm:gap-4">
                        <span class="text-sm text-gray-400 dark:text-white/30 group-hover:text-white/70 transition-colors tabular-nums">{{ $category->products_count }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-gray-300 dark:text-white/25 group-hover:text-white transition-all duration-200 group-hover:translate-x-1"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 4. WHY WIN WIN ── --}}
    <section class="py-16 sm:py-24 bg-gray-50 dark:bg-[#0d0d0f] border-t border-gray-100 dark:border-white/5" aria-labelledby="why-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row gap-10 lg:gap-24 items-start">

                {{-- Left: Big headline --}}
                <div class="lg:w-[45%]">
                    <p class="font-mono text-[10px] tracking-[0.3em] uppercase mb-5 sm:mb-6" style="color:#C8413D;">{{ __('Why Win Win') }}</p>
                    <h2 id="why-heading" class="font-display font-black text-[clamp(2.2rem,5vw,4.2rem)] leading-[0.93] uppercase text-gray-900 dark:text-white mb-6 sm:mb-10">
                        SHAH ALAM'S<br>MOST TRUSTED<br>CAR AUDIO SHOP.
                    </h2>
                    <p class="font-mono text-[11px] tracking-[0.2em] uppercase text-gray-400 dark:text-white/25 leading-relaxed">
                        1000+ {{ __('Installations') }} &nbsp;·&nbsp; 20+ {{ __('Brands') }}<br>Shah Alam Showroom
                    </p>
                </div>

                {{-- Right: Bullet list --}}
                <div class="flex-1 border-t border-gray-200 dark:border-white/10 w-full">
                    @foreach([
                        [__('Curated Selection'), __('We only stock products we personally test and stand behind. No filler, no guessing.')],
                        [__('See Before You Buy'), __('Walk into our showroom, try the products, and get honest advice. Zero pressure.')],
                        [__('WhatsApp Support'), __('Message us directly for quick answers on stock, pricing, and installation bookings.')],
                        [__('Expert Installation'), __('Our technicians have fitted thousands of cars. Clean, permanent, and done right.')],
                    ] as [$title, $desc])
                    <div class="flex gap-4 sm:gap-5 py-5 sm:py-7 border-b border-gray-200 dark:border-white/10">
                        <span class="font-bold mt-0.5 shrink-0 text-sm" style="color:#C8413D;">■</span>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base mb-1 sm:mb-1.5">{{ $title }}</h3>
                            <p class="text-gray-500 dark:text-white/40 text-sm leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── 5. 3D SHOWCASE ── --}}
    @if($showcaseProduct)
    <section class="py-16 sm:py-24 bg-gray-100 dark:bg-[#0C0C0E] border-t border-gray-200 dark:border-white/5" aria-labelledby="showcase-heading">
        <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

                <div class="text-center lg:text-left order-2 lg:order-1">
                    <p class="font-mono text-[10px] tracking-[0.3em] uppercase text-[#C8413D] mb-5 sm:mb-6">{{ __('Interactive 3D') }}</p>
                    <h2 id="showcase-heading" class="font-display font-black text-[clamp(2.2rem,4.5vw,3.8rem)] leading-[0.93] uppercase text-gray-900 dark:text-white mb-5 sm:mb-6">
                        EXPLORE<br>BEFORE<br>YOU BUY.
                    </h2>
                    <p class="text-gray-600 dark:text-white/45 text-base mb-8 sm:mb-10 max-w-sm mx-auto lg:mx-0 leading-relaxed">
                        {{ __('Rotate, zoom, and inspect every angle. See exactly what you are getting before you visit the showroom.') }}
                    </p>
                    <a href="{{ route('contact') }}"
                       class="group inline-flex items-center justify-center gap-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-8 py-4 rounded-xl font-black text-lg hover:border-[#C8413D] hover:text-[#C8413D] hover:bg-gray-50 dark:hover:bg-gray-800 hover:-translate-y-1 hover:shadow-md transition-all duration-300 active:scale-95 w-full sm:w-auto">
                        {{ __('Visit Our Showroom') }}
                        <i data-lucide="arrow-right" class="icon-sm transition-transform duration-300 group-hover:translate-x-1"></i>
                    </a>
                </div>

                {{-- Model viewer — always dark bg --}}
                <div class="order-1 lg:order-2">
                    <div class="border border-gray-300 dark:border-white/10 bg-gray-900">
                        <model-viewer
                            src="{{ $showcaseProduct->model_url }}"
                            alt="{{ __('3D car model — Win Win Car Studio') }}"
                            auto-rotate
                            auto-rotate-delay="1000"
                            rotation-per-second="20deg"
                            camera-controls
                            touch-action="pan-y"
                            shadow-intensity="1"
                            exposure="0.8"
                            style="width:100%; height:clamp(260px,50vw,440px); background:transparent;"
                            loading="lazy">
                            <div slot="progress-bar" style="display:none;"></div>
                            <div slot="poster" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:#111827;">
                                <svg style="width:40px;height:40px;color:#C8413D;animation:spin3d 1s linear infinite;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                                </svg>
                            </div>
                        </model-viewer>
                    </div>
                    {{-- Controls hint below the viewer --}}
                    <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 mt-3 text-xs text-gray-400 dark:text-white/25">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="mouse-pointer-2" class="w-3.5 h-3.5"></i>
                            {{ __('Drag to rotate') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="zoom-in" class="w-3.5 h-3.5"></i>
                            {{ __('Scroll to zoom') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
                            {{ __('Pinch to zoom on mobile') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ── 6. TESTIMONIALS ── --}}
    @if($testimonials->count() > 0)
    <section class="py-16 sm:py-24 bg-white dark:bg-[#0C0C0E] border-t border-gray-100 dark:border-white/5" aria-labelledby="testimonials-heading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <p class="font-mono text-[10px] tracking-[0.3em] uppercase text-gray-400 dark:text-white/25 mb-10 sm:mb-12" id="testimonials-heading">{{ __('What Customers Say') }}</p>

            {{-- Pull quote: first testimonial --}}
            <div class="mb-10 sm:mb-14 max-w-3xl">
                <p class="font-display text-[clamp(1.4rem,3.5vw,2.6rem)] font-black leading-tight text-gray-900 dark:text-white mb-5 sm:mb-6 uppercase">
                    "{{ $testimonials->first()->message }}"
                </p>
                <div class="flex items-center gap-3">
                    @if($testimonials->first()->image)
                        <img src="{{ Storage::url($testimonials->first()->image) }}"
                             alt="{{ $testimonials->first()->name }}"
                             class="w-9 h-9 object-cover"
                             loading="lazy">
                    @else
                        <div class="w-9 h-9 flex items-center justify-center text-white font-black text-sm shrink-0" style="background:#C8413D;">
                            {{ mb_substr($testimonials->first()->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $testimonials->first()->name }}</span>
                        @if($testimonials->first()->location)
                        <span class="text-sm text-gray-400 dark:text-white/30"> · {{ $testimonials->first()->location }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Scroll row: remaining testimonials --}}
            @if($testimonials->count() > 1)
            <div class="flex gap-3 sm:gap-4 overflow-x-auto pb-4 -mx-4 sm:-mx-6 px-4 sm:px-6 snap-x snap-mandatory scrollbar-hide">
                @foreach($testimonials->skip(1) as $testimonial)
                <article class="snap-start shrink-0 w-72 sm:w-80 border border-gray-200 dark:border-white/10 p-5 sm:p-6 bg-gray-50 dark:bg-white/5">
                    <blockquote class="text-gray-600 dark:text-white/55 text-sm leading-relaxed mb-5 sm:mb-6">
                        "{{ Str::limit($testimonial->message, 120) }}"
                    </blockquote>
                    <div class="flex items-center gap-3">
                        @if($testimonial->image)
                            <img src="{{ Storage::url($testimonial->image) }}"
                                 alt="{{ $testimonial->name }}"
                                 class="w-8 h-8 object-cover"
                                 loading="lazy">
                        @else
                            <div class="w-8 h-8 flex items-center justify-center text-white font-bold text-xs shrink-0" style="background:#C8413D;">
                                {{ mb_substr($testimonial->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $testimonial->name }}</div>
                            @if($testimonial->location)
                            <div class="text-xs text-gray-400 dark:text-white/30">{{ $testimonial->location }}</div>
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

    {{-- ── 7. CTA ── --}}
    <section style="background:#C8413D;" class="py-14 sm:py-20 text-white" aria-labelledby="cta-heading">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-8 sm:gap-10">
                <div>
                    <p class="font-mono text-[11px] tracking-[0.3em] uppercase text-white/50 mb-4 sm:mb-5">{{ __('Come Visit Us') }}</p>
                    <h2 id="cta-heading" class="font-display font-black text-[clamp(2rem,5vw,3.8rem)] leading-[0.93] uppercase">
                        COME LOOK<br>AROUND.<br>NO PRESSURE.
                    </h2>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full lg:w-auto shrink-0">
                    <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                       class="group relative inline-flex items-center justify-center gap-3 bg-[#25D366] text-white px-8 py-4 rounded-xl font-black text-lg transition-all duration-300 shadow-[0_6px_20px_rgba(37,211,102,0.3)] overflow-hidden hover:shadow-[0_10px_30px_rgba(37,211,102,0.45)] hover:-translate-y-1 active:scale-95 w-full sm:w-auto">
                        <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                        <svg class="w-5 h-5 shrink-0 relative z-10 transition-transform duration-300 group-hover:rotate-[15deg]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span class="relative z-10">WhatsApp</span>
                    </a>
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                       class="group inline-flex items-center justify-center gap-3 border-2 border-white text-white px-8 py-4 rounded-xl font-black text-lg hover:bg-white hover:text-[#C8413D] hover:-translate-y-1 hover:shadow-lg transition-all duration-300 active:scale-95 w-full sm:w-auto">
                        <i data-lucide="map-pin" class="icon-sm shrink-0"></i>
                        {{ __('Directions') }}
                    </a>
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
            /* Only stop CSS animations — do NOT override JS transforms */
            .hero-enter, .hero-enter-delay { animation: none; opacity: 1; transform: none; }
        }

        /* Category row hover — must work in both light and dark mode */
        a.cat-row:hover,
        a.cat-row:focus-visible {
            background-color: #C8413D !important;
            border-color: #C8413D !important;
        }
        a.cat-row:hover span,
        a.cat-row:hover svg,
        a.cat-row:focus-visible span,
        a.cat-row:focus-visible svg {
            color: #fff !important;
        }

        /* Hide scrollbar for testimonials row */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush

    @push('scripts')
    <script>
    (function () {
        function initMarquee() {
            const wrapper = document.querySelector('.brand-marquee-wrapper');
            const track   = document.querySelector('.brand-track');
            if (!wrapper || !track) return;
            // Respect prefers-reduced-motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const BASE_SPEED = 0.55;
            let pos = 0, speed = BASE_SPEED, target = BASE_SPEED, halfW = 0;
            function measure() { halfW = track.scrollWidth / 2; }
            measure();
            window.addEventListener('resize', measure);
            wrapper.addEventListener('mouseenter', () => { target = 0; });
            wrapper.addEventListener('mouseleave', () => { target = BASE_SPEED; });
            let rafId;
            function tick() {
                speed += (target - speed) * 0.1;
                pos   -= speed;
                if (pos <= -halfW) pos += halfW;
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
