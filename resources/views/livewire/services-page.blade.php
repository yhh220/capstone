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

    <section class="border-b border-gray-200 dark:border-gray-700/60" aria-labelledby="services-heading">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16 lg:py-20">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr] gap-10 lg:gap-16 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-4">
                        <span class="w-8 h-px bg-brand-red"></span>
                        {{ __('Installation Services') }}
                    </span>
                    <h1 id="services-heading" class="text-4xl sm:text-5xl lg:text-6xl text-brand-black dark:text-white leading-tight mb-5">
                        {{ __('Professional car upgrades, fitted properly.') }}
                    </h1>
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl">
                        {{ __('Choose a service, book an appointment, and let our team handle the installation, wiring, setup, and finishing details at the showroom.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 mt-8">
                        <a href="{{ route('booking') }}"
                           class="btn btn-primary btn-md btn-shine">
                            <svg class="icon-md" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                            {{ __('Book Appointment') }}
                        </a>
                        <a href="{{ $generalWhatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                           class="btn btn-whatsapp btn-md btn-shine">
                            <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-.9-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-1.7-.8-2.8-1.5-3.9-3.3-.3-.5.3-.5.8-1.6.1-.2 0-.4 0-.5s-.7-1.7-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4s-1 1-1 2.5 1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.4ZM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Z"></path></svg>
                            {{ __('Ask First') }}
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 sm:p-6 shadow-sm">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 p-4">
                            <div class="text-3xl font-black text-brand-red">{{ $services->count() }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-bold mt-1">{{ __('Active services') }}</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 p-4">
                            <div class="text-3xl font-black text-brand-red">3</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-bold mt-1">{{ __('Steps to book') }}</div>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 p-4">
                            <div class="text-3xl font-black text-brand-red">1</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-bold mt-1">{{ __('Showroom team') }}</div>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3">{{ __('What to prepare') }}</p>
                        <div class="grid sm:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex gap-2">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand-red shrink-0"></span>
                                <span>{{ __('Car model and year') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand-red shrink-0"></span>
                                <span>{{ __('Preferred date and time') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand-red shrink-0"></span>
                                <span>{{ __('Current setup or issue') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-brand-red shrink-0"></span>
                                <span>{{ __('Budget or product preference') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24 overflow-hidden" aria-labelledby="services-list-heading">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16 sm:mb-24">
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
                <div class="space-y-20 md:space-y-28">
                    @foreach($services as $service)
                        @php
                            $serviceWhatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I would like to enquire about ' . $service->name . '.');
                            [$blobClass, $blobAnim] = $blobStyles[$loop->index % count($blobStyles)];
                            $img = $service->getImageUrl('thumb');
                        @endphp
                        <div class="relative flex flex-col md:flex-row items-center gap-10 md:gap-16">

                            {{-- Illustration (alternates sides) --}}
                            <div class="w-full md:w-1/2 flex justify-center {{ $loop->even ? 'md:order-2' : '' }}" data-aos="zoom-in">
                                <div class="{{ $blobAnim }} relative w-48 h-48 lg:w-64 lg:h-64 flex items-center justify-center overflow-hidden shadow-xl {{ $img ? '' : $blobClass }}">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ __($service->name) }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="drop-shadow-sm">{!! str_replace('w-6 h-6', 'w-16 h-16 lg:w-20 lg:h-20', $iconFor($service->name)) !!}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="w-full md:w-1/2 text-center md:text-left {{ $loop->even ? 'md:order-1' : '' }}" data-aos="{{ $loop->even ? 'fade-right' : 'fade-left' }}" data-aos-delay="100">
                                @if($service->price)
                                    <div class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-3">
                                        {{ __('From') }} <span class="text-brand-red">RM {{ number_format((float) $service->price, 0) }}</span>
                                    </div>
                                @endif
                                <h3 class="text-2xl sm:text-3xl font-black text-brand-black dark:text-white uppercase tracking-tight leading-tight mb-4">
                                    {{ __($service->name) }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base font-medium leading-relaxed mb-6 max-w-md mx-auto md:mx-0">
                                    {{ __($service->description) }}
                                </p>

                                <div class="flex flex-wrap gap-2 justify-center md:justify-start mb-7">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-bold text-gray-600 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5 text-brand-red" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                                        {{ $service->duration_label }}
                                    </span>
                                    @if($service->buffer_after > 0)
                                        <span class="inline-flex items-center rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-bold text-gray-600 dark:text-gray-400">
                                            {{ __('Buffer') }} {{ $service->buffer_after }} {{ __('min') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                                    <a href="{{ route('booking', ['service' => $service->id]) }}" class="btn btn-primary btn-md btn-shine">
                                        <svg class="icon-md btn-ico" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                                        {{ __('Book') }}
                                    </a>
                                    <a href="{{ $serviceWhatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-md btn-shine">
                                        <svg class="icon-md btn-ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-.9-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-1.7-.8-2.8-1.5-3.9-3.3-.3-.5.3-.5.8-1.6.1-.2 0-.4 0-.5s-.7-1.7-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4s-1 1-1 2.5 1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.4ZM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Z"></path></svg>
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-10 text-center">
                    <h3 class="text-2xl text-brand-black dark:text-white mb-2">{{ __('No services available') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Please contact us on WhatsApp and our team will help you directly.') }}</p>
                </div>
            @endif
        </div>

        {{-- Organic blob shapes for the service illustrations --}}
        <style>
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

    <section class="py-14 sm:py-20" aria-labelledby="booking-process-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-10">
                <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                    <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                    {{ __('Booking Process') }}
                </span>
                <h2 id="booking-process-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white mb-3">
                    {{ __('Simple from enquiry to handover') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl">{{ __('We keep the service flow clear so you know what happens before your car goes into the workshop.') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-5 sm:gap-6">
                @foreach([
                    ['01', __('Pick a service'), __('Choose the installation or upgrade you need and share your car model details.')],
                    ['02', __('Confirm your slot'), __('Select an available appointment time or ask our team for advice on WhatsApp.')],
                    ['03', __('Arrive and install'), __('Bring your car to the showroom and we handle fitting, setup, and final checks.')],
                ] as [$number, $title, $body])
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 transition-all duration-300 hover:border-brand-red/40 hover:shadow-md">
                        <div class="w-11 h-11 rounded-xl bg-brand-red/10 text-brand-red flex items-center justify-center font-black text-base mb-5" aria-hidden="true">{{ $number }}</div>
                        <h3 class="text-2xl text-brand-black dark:text-white mb-3">{{ $title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-20" aria-labelledby="service-cta-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="relative overflow-hidden rounded-[2rem] bg-[#121212] dark:bg-[#1C1917] border border-gray-800 dark:border-gray-700 px-6 py-14 sm:px-14 sm:py-16">
                {{-- Contained accent glow --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-red/25 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
                <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-brand-red/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

                <div class="relative grid lg:grid-cols-[1.2fr_auto] gap-10 items-center">
                    <div>
                        <h2 id="service-cta-heading" class="text-3xl sm:text-5xl text-white leading-tight mb-4">
                            {{ __('Not sure which service fits your car?') }}
                        </h2>
                        <p class="text-white/70 text-base sm:text-lg max-w-xl">
                            {{ __('Send us your car model, current setup, and goal. We will recommend the right service before you book.') }}
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 shrink-0">
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
