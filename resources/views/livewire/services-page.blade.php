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

    <section class="bg-gray-100 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700" aria-labelledby="services-heading">
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
                           class="btn btn-secondary btn-md">
                            <svg class="icon-md text-[#25D366]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-.9-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-1.7-.8-2.8-1.5-3.9-3.3-.3-.5.3-.5.8-1.6.1-.2 0-.4 0-.5s-.7-1.7-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4s-1 1-1 2.5 1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.4ZM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Z"></path></svg>
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

    <section class="py-14 sm:py-20 bg-white dark:bg-brand-black" aria-labelledby="services-list-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-8 sm:mb-10">
                <div>
                    <span class="inline-block text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">{{ __('Service Menu') }}</span>
                    <h2 id="services-list-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white">
                        {{ __('Choose the right job') }}
                    </h2>
                </div>
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-brand-red transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    {{ __('Visit showroom') }}
                </a>
            </div>

            @if($services->count() > 0)
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
                    @foreach($services as $service)
                        @php
                            $serviceWhatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hi Win Win Car Studio! I would like to enquire about ' . $service->name . '.');
                        @endphp
                        <article class="group bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
                            <div class="h-40 bg-gray-100 dark:bg-gray-900 overflow-hidden">
                                @if($service->getImageUrl('thumb'))
                                    <img src="{{ $service->getImageUrl('thumb') }}"
                                         alt="{{ $service->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         loading="lazy">
                                @else
                                    <div class="h-full flex items-center justify-center text-brand-red bg-gradient-to-br from-gray-100 to-white dark:from-gray-900 dark:to-gray-800">
                                        <div class="w-16 h-16 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                                            {!! $iconFor($service->name) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5 sm:p-6 flex flex-col flex-1">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <h3 class="text-2xl text-brand-black dark:text-white leading-tight">
                                        {{ $service->name }}
                                    </h3>
                                    @if($service->price)
                                        <div class="text-right shrink-0">
                                            <div class="text-xs text-gray-400 font-bold">{{ __('From') }}</div>
                                            <div class="font-black text-brand-red">RM {{ number_format((float) $service->price, 0) }}</div>
                                        </div>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-5">
                                    {{ $service->description }}
                                </p>

                                <div class="mt-auto">
                                    <div class="flex flex-wrap gap-2 mb-5">
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-bold text-gray-600 dark:text-gray-400">
                                            <svg class="w-3.5 h-3.5 text-brand-red" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                                            {{ $service->duration_label }}
                                        </span>
                                        @if($service->buffer_after > 0)
                                            <span class="inline-flex items-center rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-bold text-gray-600 dark:text-gray-400">
                                                {{ __('Buffer') }} {{ $service->buffer_after }} {{ __('min') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('booking', ['service' => $service->id]) }}"
                                           class="btn btn-primary btn-sm btn-shine">
                                            {{ __('Book') }}
                                        </a>
                                        <a href="{{ $serviceWhatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                                           class="btn btn-secondary btn-sm">
                                            WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-10 text-center">
                    <h3 class="text-2xl text-brand-black dark:text-white mb-2">{{ __('No services available') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Please contact us on WhatsApp and our team will help you directly.') }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="py-14 sm:py-20 bg-gray-100 dark:bg-gray-900 border-y border-gray-200 dark:border-gray-700" aria-labelledby="booking-process-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-block text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">{{ __('Booking Process') }}</span>
                <h2 id="booking-process-heading" class="text-3xl sm:text-4xl text-brand-black dark:text-white mb-3">
                    {{ __('Simple from enquiry to handover') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">{{ __('We keep the service flow clear so you know what happens before your car goes into the workshop.') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-5 sm:gap-6">
                @foreach([
                    ['01', __('Pick a service'), __('Choose the installation or upgrade you need and share your car model details.')],
                    ['02', __('Confirm your slot'), __('Select an available appointment time or ask our team for advice on WhatsApp.')],
                    ['03', __('Arrive and install'), __('Bring your car to the showroom and we handle fitting, setup, and final checks.')],
                ] as [$number, $title, $body])
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                        <div class="text-4xl font-black text-brand-red mb-5">{{ $number }}</div>
                        <h3 class="text-2xl text-brand-black dark:text-white mb-3">{{ $title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-20 bg-brand-red text-white" aria-labelledby="service-cta-heading">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 id="service-cta-heading" class="text-3xl sm:text-5xl leading-tight mb-5">
                {{ __('Not sure which service fits your car?') }}
            </h2>
            <p class="text-white/85 text-base sm:text-lg max-w-2xl mx-auto mb-8">
                {{ __('Send us your car model, current setup, and goal. We will recommend the right service before you book.') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ $generalWhatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                   class="group relative inline-flex items-center justify-center bg-white text-brand-red px-8 py-4 sm:px-12 sm:py-5 rounded-full font-black text-lg sm:text-xl transition-all duration-300 shadow-[0_10px_40px_rgba(0,0,0,0.15)] overflow-hidden hover:shadow-[0_15px_50px_rgba(0,0,0,0.25)] hover:-translate-y-2 active:scale-95 w-full sm:w-auto">
                    <span class="absolute inset-0 bg-brand-red/10 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                    <span class="relative z-10 flex items-center gap-3">
                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:rotate-[15deg]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ __('Chat on WhatsApp') }}
                    </span>
                </a>
                <a href="{{ route('booking') }}"
                   class="group inline-flex items-center justify-center border-2 border-white/80 px-8 py-4 sm:px-12 sm:py-5 rounded-full font-black text-lg sm:text-xl text-white hover:bg-white hover:text-brand-red hover:border-white hover:shadow-2xl hover:-translate-y-2 active:scale-95 transition-all duration-500 w-full sm:w-auto">
                    <svg class="w-6 h-6 mr-3 transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                    {{ __('Open Booking Form') }}
                </a>
            </div>
        </div>
    </section>
</div>
