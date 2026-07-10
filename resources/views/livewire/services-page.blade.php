<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $generalWhatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I would like to ask about your installation services.');
        $mapUrl = 'https://www.google.com/maps?cid=' . config('services.store.place_cid');

        $serviceIcons = [
            'audio' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
            'subwoofer' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="4"></circle><path d="M12 3v2M12 19v2M3 12h2M19 12h2"></path></svg>',
            'tint' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M7 9h10M7 13h6"></path></svg>',
            'camera' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3Z"></path><circle cx="12" cy="13" r="3"></circle></svg>',
            'security' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="m9 12 2 2 4-5"></path></svg>',
            'tuning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3"></path><path d="M2 14h4M10 8h4M18 16h4"></path></svg>',
            'default' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9l-3.8 3.8Z"></path></svg>',
        ];

        $iconFor = function (string $name) use ($serviceIcons): string {
            $needle = strtolower($name);

            return match (true) {
                str_contains($needle, 'subwoofer'), str_contains($needle, 'amplifier') => $serviceIcons['subwoofer'],
                str_contains($needle, 'tint') => $serviceIcons['tint'],
                str_contains($needle, 'dashcam'), str_contains($needle, 'camera') => $serviceIcons['camera'],
                str_contains($needle, 'alarm'), str_contains($needle, 'security') => $serviceIcons['security'],
                str_contains($needle, 'dsp'), str_contains($needle, 'tuning'), str_contains($needle, 'calibration') => $serviceIcons['tuning'],
                str_contains($needle, 'audio'), str_contains($needle, 'speaker') => $serviceIcons['audio'],
                default => $serviceIcons['default'],
            };
        };
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 id="services-heading" class="text-4xl sm:text-5xl font-black leading-tight mb-4">
                {{ __('Professional car upgrades, fitted properly.') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-base sm:text-lg max-w-2xl mx-auto">
                {{ __('Choose a service, book an appointment, and let our team handle the installation, wiring, setup, and finishing details at the showroom.') }}
            </p>
        </div>
    </div>

    <section class="py-16 sm:py-24 overflow-hidden" aria-labelledby="services-list-heading">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-16 sm:mb-20">
                <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                    <span class="w-8 h-px bg-brand-red"></span>{{ __('Service Menu') }}<span class="w-8 h-px bg-brand-red"></span>
                </span>
                <h2 id="services-list-heading" class="text-3xl sm:text-5xl font-black text-brand-black dark:text-white uppercase tracking-tight">
                    {{ __('Choose the right job') }}
                </h2>
            </div>

            @if($services->count() > 0)
                @php
                    // Cycle blob colour + morph animation for variety (icon blobs only).
                    $blobStyles = [
                        ['bg-brand-red/10 dark:bg-brand-red/15 text-brand-red', 'svc-blob'],
                        ['bg-gray-900 text-brand-red',                           'svc-blob-alt'],
                        ['bg-brand-red-solid text-white',                        'svc-blob'],
                        ['bg-gray-800 text-brand-yellow',                        'svc-blob-alt'],
                    ];
                @endphp

                {{-- A winding road that drives through every service "stop": an
                     S-curve built at runtime through the real node positions,
                     with a car that follows it (and a brand-red trail) as you
                     scroll. The stops light up when the car passes them. --}}
                {{-- pb reserves the tarmac at the end of the road for the parking bay --}}
                <div class="relative pb-44 md:pb-48"
                     x-data="serviceRoad"
                     x-ref="flowTrack"
                     @scroll.window.passive="onScroll()">

                    {{-- The road: parking bay first so the tarmac and trail draw OVER
                         its entrance — the bay must never cover the road's run-out. --}}
                    <svg class="absolute inset-0 w-full h-full z-0 pointer-events-none" aria-hidden="true">
                        <g x-ref="bay" style="display: none">
                            <rect x="-24" y="-38" width="48" height="76" rx="9" class="svc-park-bay"/>
                            <text x="0" y="27" text-anchor="middle" dominant-baseline="central" class="svc-park-p">P</text>
                        </g>
                        <path x-ref="casing" class="svc-road-casing" fill="none"/>
                        <path x-ref="dash" class="svc-road-dash" fill="none"/>
                        <path x-ref="trail" class="svc-road-trail" fill="none"/>
                    </svg>

                    {{-- The car (top view, nose down the page) that drives the road.
                         Centring lives in the transform maths (not margin classes),
                         so the anchor can never drift off the path. --}}
                    <div x-ref="car" class="absolute top-0 left-0 z-30 pointer-events-none"
                         style="width: 30px; height: 50px; transform: translate(-9999px, -9999px); filter: drop-shadow(0 6px 8px rgba(0,0,0,0.35));">
                        <svg class="block" width="30" height="50" viewBox="0 0 34 56" aria-hidden="true">
                            <rect x="0.5" y="9" width="5" height="11" rx="2.5" fill="#111827" opacity="0.9"/>
                            <rect x="28.5" y="9" width="5" height="11" rx="2.5" fill="#111827" opacity="0.9"/>
                            <rect x="0.5" y="36" width="5" height="11" rx="2.5" fill="#111827" opacity="0.9"/>
                            <rect x="28.5" y="36" width="5" height="11" rx="2.5" fill="#111827" opacity="0.9"/>
                            <path d="M 9 2 H 25 Q 29.5 2 29.5 8 V 48 Q 29.5 54 24 54 H 10 Q 4.5 54 4.5 48 V 8 Q 4.5 2 9 2 Z" fill="rgb(var(--brand-red-rgb))"/>
                            <path d="M 9 10 Q 17 7 25 10 L 23.5 16 Q 17 13.5 10.5 16 Z" fill="#0f172a" opacity="0.8"/>
                            <rect x="8.5" y="18" width="17" height="14" rx="4" fill="#000000" opacity="0.15"/>
                            <path d="M 10.5 34 Q 17 36.5 23.5 34 L 25 41 Q 17 44.5 9 41 Z" fill="#0f172a" opacity="0.85"/>
                            <circle cx="8.5" cy="51.5" r="1.8" fill="#fef3c7" opacity="0.95"/>
                            <circle cx="25.5" cy="51.5" r="1.8" fill="#fef3c7" opacity="0.95"/>
                        </svg>
                    </div>

                    <div class="relative space-y-16 md:space-y-24">
                        @foreach($services as $service)
                            @php
                                [$blobClass, $blobAnim] = $blobStyles[$loop->index % count($blobStyles)];
                                $img = $service->getImageUrl('thumb');
                                $odd = $loop->odd; // content on the left (text-right) for odd rows
                            @endphp
                            <div class="relative flex flex-col md:flex-row items-center">

                                {{-- Content --}}
                                <div class="w-full md:w-1/2 pl-14 md:pl-0 text-left {{ $odd ? 'md:order-1 md:pr-14 md:text-right' : 'md:order-2 md:pl-14' }}"
                                     data-aos="{{ $odd ? 'fade-right' : 'fade-left' }}">
                                    <h3 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white uppercase tracking-tight leading-tight mb-4">
                                        {{ $service->localized_name }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base font-medium leading-relaxed max-w-sm {{ $odd ? 'md:ml-auto' : '' }}">
                                        {{ $service->localized_description }}
                                    </p>
                                    <a href="{{ route('booking', ['service' => $service->id]) }}"
                                       class="inline-flex items-center gap-1.5 mt-4 text-sm font-bold text-brand-red hover:underline underline-offset-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                        {{ __('Book this service') }} <span aria-hidden="true">→</span>
                                    </a>
                                </div>

                                {{-- Node on the line (no number) --}}
                                <div class="step-node absolute left-8 md:left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-white dark:bg-gray-900 border-2 z-20 flex items-center justify-center transition-all duration-300"
                                     :class="activeStep >= {{ $loop->iteration }} ? 'border-brand-red scale-110 shadow-md shadow-brand-red/20' : 'border-gray-300 dark:border-gray-600'">
                                    <span class="w-2 h-2 rounded-full transition-colors duration-300" :class="activeStep >= {{ $loop->iteration }} ? 'bg-brand-red' : 'bg-transparent'"></span>
                                </div>

                                {{-- Illustration (hidden on mobile) --}}
                                <div class="hidden md:flex md:w-1/2 justify-center {{ $odd ? 'md:order-2 md:pl-14' : 'md:order-1 md:pr-14' }}" data-aos="zoom-in">
                                    <div class="{{ $blobAnim }} w-28 h-28 lg:w-36 lg:h-36 flex items-center justify-center overflow-hidden shadow-lg {{ $img ? '' : $blobClass }}">
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $service->localized_name }}" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="drop-shadow-sm">{!! str_replace('w-6 h-6', 'w-10 h-10 lg:w-12 lg:h-12', $iconFor($service->name)) !!}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-10 text-center">
                    <h3 class="text-2xl text-brand-black dark:text-white mb-2">{{ __('No services available') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Please contact us on WhatsApp and our team will help you directly.') }}</p>
                </div>
            @endif
        </div>

        {{-- Road styling + organic blob shapes --}}
        <style>
            .svc-road-casing {
                stroke: #374151;
                stroke-width: 22;
                stroke-linecap: round;
                stroke-linejoin: round;
            }
            .dark .svc-road-casing { stroke: #4b5563; }
            .svc-road-dash {
                stroke: #f9fafb;
                stroke-width: 2.5;
                stroke-dasharray: 10 14;
                stroke-linecap: round;
                opacity: 0.95;
            }
            .dark .svc-road-dash { stroke: #d1d5db; }
            /* The travelled part of the route — same brand-red glow the old
               straight track used, drawn over the centre line behind the car.
               NO transition: the trail and the car share one arc length per
               frame, and any easing here would visibly lag it off the car. */
            .svc-road-trail {
                stroke: rgb(var(--brand-red-rgb));
                stroke-width: 4;
                stroke-linecap: round;
                filter: drop-shadow(0 0 6px rgba(var(--brand-red-rgb), 0.5));
                will-change: stroke-dashoffset;
            }
            /* Parking bay at the end of the road */
            .svc-park-bay {
                fill: #374151;
                stroke: #f9fafb;
                stroke-width: 2.5;
                stroke-dasharray: 8 6;
            }
            .dark .svc-park-bay { fill: #4b5563; stroke: #d1d5db; }
            .svc-park-p {
                fill: #f9fafb;
                font-family: ui-sans-serif, system-ui, sans-serif;
                font-size: 17px;
                font-weight: 900;
                opacity: 0.9;
            }
            .dark .svc-park-p { fill: #d1d5db; }
            @media (max-width: 767px) {
                .svc-road-casing { stroke-width: 15; }
                .svc-road-dash { stroke-width: 2; stroke-dasharray: 7 10; }
            }
            @keyframes svcBlob {
                0%   { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
                50%  { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
                100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            }
            @keyframes svcBlobAlt {
                0%   { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
                50%  { border-radius: 70% 30% 40% 60% / 60% 40% 50% 60%; }
                100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
            }
            .svc-blob     { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; animation: svcBlob 8s ease-in-out infinite; }
            .svc-blob-alt { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: svcBlobAlt 9s ease-in-out infinite; }
            .svc-blob:hover, .svc-blob-alt:hover { animation-duration: 3s; filter: brightness(1.04); }
            @media (prefers-reduced-motion: reduce) {
                .svc-blob, .svc-blob-alt { animation: none; border-radius: 1.75rem; }
            }
        </style>

        @assets
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('serviceRoad', () => ({
                    activeStep: 0,
                    len: 0,
                    target: 0,   // arc length the scroll position asks for
                    cur: -1,     // arc length the car is actually at (-1 = snap on first frame)
                    rafId: null,
                    lastTs: null,

                    init() {
                        // Layout must settle (fonts, AOS offsets, images) before the
                        // node positions are worth measuring; the ResizeObserver
                        // then keeps the road glued to the rows as they reflow.
                        this.$nextTick(() => this.build());
                        this.onLoad = () => this.build();
                        window.addEventListener('load', this.onLoad);
                        this.ro = new ResizeObserver(() => this.build());
                        this.ro.observe(this.$refs.flowTrack);
                        // Scroll events arrive in coarse steps (a mouse wheel moves
                        // ~100px per notch), so writing the car's position straight
                        // from them made it hop stop-motion-style. The scroll handler
                        // only sets a target; this rAF loop eases the car toward it
                        // every frame — smooth, and it reads like braking/accelerating.
                        this.reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                        this.rafId = requestAnimationFrame((ts) => this.animate(ts));
                    },

                    destroy() {
                        window.removeEventListener('load', this.onLoad);
                        this.ro?.disconnect();
                        if (this.rafId !== null) cancelAnimationFrame(this.rafId);
                    },

                    animate(ts) {
                        if (!this.$refs.flowTrack.isConnected) { this.rafId = null; return; }
                        this.rafId = requestAnimationFrame((t) => this.animate(t));
                        if (!this.len) return;

                        const dt = this.lastTs === null ? 0 : Math.min((ts - this.lastTs) / 1000, 0.1);
                        this.lastTs = ts;

                        if (this.cur < 0 || this.reduced) {
                            this.cur = this.target; // first frame / reduced motion: no chase
                        } else {
                            this.cur += (this.target - this.cur) * Math.min(dt * 7, 1);
                            if (Math.abs(this.target - this.cur) < 0.15) this.cur = this.target;
                        }

                        // Trail and car are driven by the SAME arc length — in lockstep.
                        this.$refs.trail.style.strokeDashoffset = this.len - this.cur;
                        const p = this.$refs.casing.getPointAtLength(this.cur);
                        const q = this.$refs.casing.getPointAtLength(Math.min(this.len, this.cur + 1));
                        // At the very end q ≈ p; keep the last heading (parked straight).
                        const ang = (q.y === p.y && q.x === p.x) ? 0 : Math.atan2(q.y - p.y, q.x - p.x) * 180 / Math.PI - 90;
                        // -15/-25 centres the 30x50 car on the path point; rotation then
                        // spins around the element's own centre, i.e. that same point.
                        this.$refs.car.style.transform = `translate(${p.x - 15}px, ${p.y - 25}px) rotate(${ang}deg)`;
                    },

                    // Build one smooth serpentine path THROUGH every stop, ending in
                    // the parking bay: cubic segments whose control points bulge to
                    // alternating sides at each gap's vertical midpoint. The bulge is
                    // capped relative to the gap's height so a short final gap can
                    // never fold the road into a hairpin.
                    build() {
                        const track = this.$refs.flowTrack;
                        const nodes = track.querySelectorAll('.step-node');
                        if (!nodes.length) return;

                        const box = track.getBoundingClientRect();
                        const stops = [...nodes].map((n) => {
                            const r = n.getBoundingClientRect();
                            return { x: r.left + r.width / 2 - box.left, y: r.top + r.height / 2 - box.top };
                        });
                        // The bay sits in the reserved bottom padding, on the centre axis.
                        const bayAt = { x: stops[stops.length - 1].x, y: box.height - 60 };
                        const pts = [{ x: stops[0].x, y: 0 }, ...stops, bayAt];

                        const amp = window.innerWidth >= 768 ? 56 : 9;
                        let d = `M ${pts[0].x} ${pts[0].y}`;
                        for (let i = 1; i < pts.length; i++) {
                            const a = pts[i - 1], b = pts[i], dir = i % 2 ? 1 : -1;
                            // Gentler sweep into the bay so the car parks near-straight.
                            const scale = i === pts.length - 1 ? 0.18 : 0.35;
                            const bow = Math.min(amp, (b.y - a.y) * scale) * dir;
                            const my = (a.y + b.y) / 2;
                            d += ` C ${a.x + bow} ${my}, ${b.x + bow} ${my}, ${b.x} ${b.y}`;
                        }

                        ['casing', 'dash', 'trail'].forEach((k) => this.$refs[k].setAttribute('d', d));
                        this.len = this.$refs.casing.getTotalLength();
                        this.$refs.trail.style.strokeDasharray = this.len;

                        // Park the bay markings at the road's end.
                        this.$refs.bay.setAttribute('transform', `translate(${bayAt.x}, ${bayAt.y})`);
                        this.$refs.bay.style.display = 'block';

                        // Pre-sample the path so the car can be placed by SCREEN HEIGHT.
                        // Progress-by-arc-length drifted: curvy stretches pack more road
                        // per pixel of page, so the car slid ahead of / behind the line
                        // the stops light up on. Mapping the 60%-viewport line to the
                        // path point AT that height keeps car, trail and stops in step.
                        this.samples = [];
                        const steps = Math.max(64, Math.round(this.len / 6));
                        for (let i = 0; i <= steps; i++) {
                            const l = (this.len * i) / steps;
                            this.samples.push({ l, y: this.$refs.casing.getPointAtLength(l).y });
                        }

                        this.onScroll();
                    },

                    // Scroll only sets the target — the rAF loop above does the driving.
                    onScroll() {
                        if (!this.len || !this.samples) return;
                        const track = this.$refs.flowTrack;
                        const r = track.getBoundingClientRect();
                        const mid = window.innerHeight * 0.6;
                        const yTarget = Math.max(0, Math.min(r.height, mid - r.top));

                        // Binary-search the arc length whose road point sits at that
                        // height (y grows monotonically down our path).
                        let lo = 0, hi = this.samples.length - 1;
                        while (lo < hi) {
                            const m = (lo + hi) >> 1;
                            if (this.samples[m].y < yTarget) lo = m + 1; else hi = m;
                        }
                        this.target = this.samples[lo].l;

                        // Stops light up as the car passes (same rule as before).
                        let cur = 0;
                        track.querySelectorAll('.step-node').forEach((n, i) => {
                            if (n.getBoundingClientRect().top < mid + 20) cur = i + 1;
                        });
                        this.activeStep = cur;
                    },
                }));
            });
        </script>
        @endassets
    </section>

    <section class="py-14 sm:py-20" aria-labelledby="service-cta-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="relative overflow-hidden rounded-[2rem] bg-[#121212] dark:bg-[#1C1917] border border-gray-800 dark:border-gray-700 px-6 py-14 sm:px-14 sm:py-16">
                {{-- Contained accent glow --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-red/25 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
                <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-brand-red/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

                <div class="relative grid lg:grid-cols-[1.2fr_auto] gap-10 items-center text-center lg:text-left">
                    <div>
                        <h2 id="service-cta-heading" class="text-3xl sm:text-5xl text-white leading-tight mb-4">
                            {{ __('Not sure which service fits your car?') }}
                        </h2>
                        <p class="text-white/70 text-base sm:text-lg max-w-xl mx-auto lg:mx-0">
                            {{ __('Send us your car model, current setup, and goal. We will recommend the right service before you book.') }}
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 shrink-0 justify-center lg:justify-start items-center lg:items-stretch">
                        <x-btn.whatsapp :href="$generalWhatsAppUrl" size="btn-lg">{{ __('Chat on WhatsApp') }}</x-btn.whatsapp>
                        <a href="{{ route('booking') }}" class="btn btn-outline-light btn-lg">
                            <svg class="icon-md btn-ico" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                            {{ __('Open Booking Form') }}
                        </a>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light btn-lg">
                            <svg class="icon-md btn-ico" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            {{ __('Visit the Showroom') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
