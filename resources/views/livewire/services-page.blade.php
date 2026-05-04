<div>
    @php
    $waBase    = 'https://wa.me/' . config('services.store.phone_raw') . '?text=';
    $waGeneral = $waBase . rawurlencode('Hi Win Win Car Studio! I would like to book a service.');

    // Four capability pillars — each groups related services
    $pillars = [
        [
            'num'      => '01',
            'title'    => 'AUDIO &<br>ENTERTAINMENT',
            'tagline'  => __('Sound That Moves You'),
            'body'     => __('From factory-grade upgrades to full competition-level builds — we design and install car audio systems that transform how you experience every drive.'),
            'services' => ['Car Audio Installation', 'Subwoofer & Amplifier Setup', 'DSP Tuning & Sound Calibration'],
            'cta_wa'   => $waBase . rawurlencode('Hi Win Win! I\'d like to enquire about your car audio installation services.'),
        ],
        [
            'num'      => '02',
            'title'    => 'SECURITY &<br>PROTECTION',
            'tagline'  => __('Peace of Mind, Every Park'),
            'body'     => __('Protect your investment with industry-leading security systems. Authorised Viper installation, full alarm wiring, and dashcam setups — done right the first time.'),
            'services' => ['Viper Alarm', 'Car Alarm & Security System', 'Dashcam Installation'],
            'cta_wa'   => $waBase . rawurlencode('Hi Win Win! I\'d like to enquire about your car security and alarm services.'),
        ],
        [
            'num'      => '03',
            'title'    => 'STYLING &<br>MODIFICATION',
            'tagline'  => __('Your Vision. Your Build.'),
            'body'     => __('Reshape your car\'s identity — inside and out. Body kits, premium interior finishing, custom plates and lighting upgrades that make every detail count.'),
            'services' => ['Body Kit', 'Seat Cover', 'Steering Cover', 'Car Plate', 'Head Lamp'],
            'cta_wa'   => $waBase . rawurlencode('Hi Win Win! I\'d like to enquire about car styling and modification services.'),
        ],
        [
            'num'      => '04',
            'title'    => 'COMFORT &<br>CLIMATE',
            'tagline'  => __('Drive Better, Every Day'),
            'body'     => __('Stay cool and comfortable year-round. Full air conditioning servicing and professional window film installation — comfort and privacy in Malaysia\'s climate.'),
            'services' => ['Air Conditioning', 'Window Tinting'],
            'cta_wa'   => $waBase . rawurlencode('Hi Win Win! I\'d like to enquire about your air conditioning and tinting services.'),
        ],
    ];
    @endphp

    {{-- ══════════════════════════════
         HERO
    ══════════════════════════════ --}}
    <section class="relative min-h-[85vh] flex flex-col justify-end overflow-hidden bg-[#0C0C0E] pb-16 sm:pb-24"
             aria-label="{{ __('Services hero') }}">

        {{-- Background --}}
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="svc-grid absolute inset-0 opacity-[0.035]"></div>
            <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-brand-red/10 blur-[130px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-brand-red/5 blur-[80px] rounded-full"></div>
        </div>

        {{-- Ghost text --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden" aria-hidden="true">
            <span class="font-display text-[clamp(6rem,22vw,18rem)] text-white/[0.025] uppercase leading-none tracking-tighter select-none">SERVICES</span>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">

            <div class="hero-reveal mb-8">
                <span class="inline-flex items-center gap-2 text-brand-red text-xs font-black uppercase tracking-[0.25em]">
                    <span class="w-8 h-px bg-brand-red"></span>
                    {{ __('What We Do') }}
                </span>
            </div>

            <h1 class="hero-reveal-delay1 font-display text-[clamp(3.5rem,10vw,8rem)] text-white leading-[0.88] mb-8 max-w-4xl">
                {{ strtoupper(__('Professional Installation &')) }}<br>
                <span class="svc-gradient-text">{{ strtoupper(__('Modification Services')) }}</span>
            </h1>

            <div class="hero-reveal-delay2 flex flex-wrap items-center gap-6">
                <a href="{{ route('booking') }}"
                   class="group inline-flex items-center gap-2.5 bg-brand-red text-white px-7 py-3.5 rounded-full font-bold text-sm tracking-wide transition-all duration-300 hover:shadow-[0_0_30px_rgba(200,65,61,0.4)] hover:-translate-y-0.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01M12 14h.01"/></svg>
                    {{ __('Book Appointment') }}
                </a>
                <div class="flex items-center gap-6 text-white/25 text-xs font-bold uppercase tracking-widest">
                    @foreach($pillars as $p)
                    <span>{{ $p['num'] }}</span>
                    @endforeach
                    <span class="text-white/10">— 4 {{ strtoupper(__('Specialisations')) }}</span>
                </div>
            </div>
        </div>

        {{-- Scroll cue --}}
        <div class="absolute bottom-8 right-8 flex items-center gap-3 text-white/20" aria-hidden="true">
            <span class="text-[9px] uppercase tracking-[0.3em] font-black">{{ __('Scroll') }}</span>
            <div class="w-12 h-px bg-gradient-to-r from-white/20 to-transparent svc-line-pulse"></div>
        </div>
    </section>

    {{-- ══════════════════════════════
         PILLARS
    ══════════════════════════════ --}}
    @foreach($pillars as $i => $pillar)
    @php $even = ($i % 2 === 0); @endphp

    <section class="svc-pillar relative overflow-hidden {{ $even ? 'bg-[#0C0C0E]' : 'bg-[#0f0f11]' }}"
             aria-label="{{ strip_tags($pillar['title']) }}">

        {{-- Subtle accent blob --}}
        <div class="absolute {{ $even ? '-right-40 top-0' : '-left-40 bottom-0' }} w-[500px] h-[500px] bg-brand-red/[0.04] blur-[100px] rounded-full pointer-events-none" aria-hidden="true"></div>

        {{-- Divider line top --}}
        <div class="h-px bg-white/[0.06]"></div>

        <div class="max-w-7xl mx-auto px-6 py-20 sm:py-28">
            <div class="grid lg:grid-cols-[1fr_1.2fr] gap-12 lg:gap-20 items-start {{ !$even ? 'lg:[&>*:first-child]:order-2 lg:[&>*:last-child]:order-1' : '' }}">

                {{-- LEFT: Number + Title --}}
                <div class="svc-reveal">
                    <div class="flex items-start gap-4 mb-6">
                        <span class="font-display text-[5rem] sm:text-[7rem] leading-none text-white/[0.07] select-none -mt-3">{{ $pillar['num'] }}</span>
                        <div class="pt-3">
                            <div class="w-8 h-px bg-brand-red mb-3"></div>
                            <p class="text-brand-red text-[10px] font-black uppercase tracking-[0.25em]">{{ $pillar['tagline'] }}</p>
                        </div>
                    </div>

                    <h2 class="font-display text-[clamp(2.8rem,6vw,5.5rem)] text-white leading-[0.9] -mt-4">
                        {!! $pillar['title'] !!}
                    </h2>
                </div>

                {{-- RIGHT: Body + Services list + CTA --}}
                <div class="svc-reveal-delayed lg:pt-4">
                    <p class="text-white/45 text-lg leading-relaxed mb-10 max-w-lg">
                        {{ $pillar['body'] }}
                    </p>

                    {{-- Services list --}}
                    <div class="mb-10">
                        <p class="text-white/20 text-[10px] font-black uppercase tracking-[0.3em] mb-4">{{ __('Includes') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($pillar['services'] as $svcName)
                            <span class="inline-flex items-center gap-2 border border-white/[0.1] text-white/50 text-xs font-semibold px-4 py-2 rounded-full">
                                <span class="w-1 h-1 rounded-full bg-brand-red/60 shrink-0"></span>
                                {{ $svcName }}
                            </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- CTAs --}}
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('booking') }}"
                           class="group relative inline-flex items-center gap-2.5 bg-white/5 border border-white/10 hover:bg-brand-red hover:border-brand-red text-white px-6 py-3 rounded-full font-bold text-sm tracking-wide overflow-hidden transition-all duration-300 hover:-translate-y-0.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01M12 14h.01"/></svg>
                            {{ __('Book Now') }}
                        </a>
                        <a href="{{ $pillar['cta_wa'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2.5 text-white/40 hover:text-white text-sm font-bold tracking-wide transition-colors duration-300 group">
                            <svg class="w-4 h-4 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            {{ __('Enquire on WhatsApp') }}
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endforeach

    {{-- ══════════════════════════════
         BOTTOM CTA
    ══════════════════════════════ --}}
    <section class="relative bg-[#0C0C0E] overflow-hidden" aria-label="{{ __('Contact CTA') }}">
        <div class="h-px bg-white/[0.06]"></div>
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[300px] bg-brand-red/8 blur-[100px] rounded-full"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 py-24 sm:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center svc-reveal">
                <div>
                    <span class="flex items-center gap-3 text-brand-red text-[10px] font-black uppercase tracking-[0.3em] mb-6">
                        <span class="w-8 h-px bg-brand-red"></span>
                        {{ __('Get In Touch') }}
                    </span>
                    <h2 class="font-display text-[clamp(2.5rem,6vw,5rem)] text-white leading-[0.9] mb-0">
                        {{ strtoupper(__("Can't find what you need?")) }}
                    </h2>
                </div>
                <div class="lg:pl-12 lg:border-l lg:border-white/[0.07]">
                    <p class="text-white/40 text-lg leading-relaxed mb-8">
                        {{ __('Our team is ready to advise you on the best options for your car. Chat with us on WhatsApp.') }}
                    </p>
                    <a href="{{ $waGeneral }}" target="_blank" rel="noopener noreferrer"
                       class="group relative inline-flex items-center gap-3 bg-[#25D366] text-white px-8 py-4 rounded-full font-bold text-sm overflow-hidden transition-all duration-300 hover:shadow-[0_0_40px_rgba(37,211,102,0.3)] hover:-translate-y-0.5 active:scale-95">
                        <span class="absolute inset-0 bg-white/15 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                        <svg class="w-5 h-5 relative z-10" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span class="relative z-10">{{ __('WhatsApp Us') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .svc-grid {
            background-image:
                linear-gradient(rgba(255,255,255,1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .svc-gradient-text {
            background: linear-gradient(90deg, #E86460, #C8413D, #E86460);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: svcShift 5s linear infinite;
        }
        @keyframes svcShift { to { background-position: 200% center; } }

        .svc-line-pulse {
            animation: svcLinePulse 2.5s ease-in-out infinite;
        }
        @keyframes svcLinePulse {
            0%, 100% { opacity: 0.15; transform: scaleX(1); }
            50%       { opacity: 0.5;  transform: scaleX(1.3); transform-origin: left; }
        }

        /* Scroll reveal */
        .svc-reveal, .svc-reveal-delayed {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.85s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.85s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .svc-reveal-delayed {
            transition-delay: 0.15s;
        }
        .svc-reveal.in-view,
        .svc-reveal-delayed.in-view {
            opacity: 1;
            transform: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .svc-gradient-text { animation: none; }
            .svc-reveal, .svc-reveal-delayed { opacity: 1; transform: none; transition: none; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
    (function () {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                e.target.classList.add('in-view');
                io.unobserve(e.target);
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.svc-reveal, .svc-reveal-delayed').forEach(el => io.observe(el));
    }());
    </script>
    @endpush
</div>
