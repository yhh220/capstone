<div>
    @php
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $generalWhatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I would like to ask about your installation services.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);

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
        <div class="max-w-5xl mx-auto px-6">
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
                        ['bg-brand-red text-white',                              'svc-blob'],
                        ['bg-gray-800 text-brand-yellow',                        'svc-blob-alt'],
                    ];
                @endphp

                {{-- Vertical track that fills as you scroll (no step numbers) --}}
                <div class="relative"
                     x-data="{ scrollProgress: 0, activeStep: 0 }"
                     x-ref="flowTrack"
                     @scroll.window="
                        let c = $refs.flowTrack; let r = c.getBoundingClientRect();
                        let mid = window.innerHeight * 0.6;
                        scrollProgress = Math.max(0, Math.min(100, ((mid - r.top) / r.height) * 100));
                        let nodes = c.querySelectorAll('.step-node'); let cur = 0;
                        nodes.forEach((n, i) => { if (n.getBoundingClientRect().top < mid + 20) cur = i + 1; });
                        activeStep = cur;
                     ">

                    {{-- The line + its fill --}}
                    <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700 md:-translate-x-1/2 z-0">
                        <div class="w-full flow-path" :style="`height: ${scrollProgress}%`"></div>
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
                                <div class="w-full md:w-1/2 pl-20 md:pl-0 text-left {{ $odd ? 'md:order-1 md:pr-14 md:text-right' : 'md:order-2 md:pl-14' }}"
                                     data-aos="{{ $odd ? 'fade-right' : 'fade-left' }}">
                                    <h3 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white uppercase tracking-tight leading-tight mb-4">
                                        {{ __($service->name) }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base font-medium leading-relaxed max-w-sm {{ $odd ? 'md:ml-auto' : '' }}">
                                        {{ __($service->description) }}
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
                                            <img src="{{ $img }}" alt="{{ __($service->name) }}" class="w-full h-full object-cover" loading="lazy">
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

        {{-- Track fill + organic blob shapes --}}
        <style>
            .flow-path {
                background: linear-gradient(to bottom, rgba(var(--brand-red-rgb), 0.6), rgb(var(--brand-red-rgb)));
                border-radius: 9999px;
                box-shadow: 0 0 12px rgba(var(--brand-red-rgb), 0.45);
                transition: height 0.12s ease-out;
                will-change: height;
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
                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 shrink-0 justify-center lg:justify-start items-center lg:items-stretch">
                        <x-btn.whatsapp :href="$generalWhatsAppUrl" size="btn-lg">{{ __('Chat on WhatsApp') }}</x-btn.whatsapp>
                        <a href="{{ route('booking') }}" class="btn btn-outline-light btn-lg">
                            <svg class="icon-md btn-ico" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                            {{ __('Open Booking Form') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
