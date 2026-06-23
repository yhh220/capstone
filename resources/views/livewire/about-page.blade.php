<div>
    @php
        $storeName = config('services.store.name');
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to know more about ' . $storeName . '.');
        $mapUrl = 'https://www.google.com/maps?cid=' . config('services.store.place_cid');
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4" data-aos="fade-up">
                {{ __('About') }} <span class="text-brand-yellow">Win Win</span>
            </h1>
            <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                {{ __('A showroom-first brand focused on trust, product visibility, and real customer conversations.') }}
            </p>
        </div>
    </div>

    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="story-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.2em] text-brand-red mb-3">
                        <span class="w-8 h-px bg-brand-red" aria-hidden="true"></span>
                        {{ __('Our Story') }}
                    </span>
                    <h2 id="story-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-6">
                        {{ __('We help customers choose with confidence') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                        {{ __('Win Win Car Audio Auto Accessories was built around a simple idea: customers should be able to discover products online, then speak to real people before deciding what fits their car.') }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('Starting from a small workshop in Kuala Lumpur, the team focused on practical advice, in-person product viewing, and honest recommendations instead of pushing quick online purchases.') }}
                    </p>
                </div>
                <div class="rounded-2xl overflow-hidden shadow-md" data-aos="fade-left" data-aos-delay="100">
                    <img src="{{ asset('images/storefront.png') }}" alt="{{ __('Win Win Car Audio showroom') }}" class="w-full h-auto" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white" aria-label="{{ __('Company statistics') }}">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([
                    [10, '+', '', __('Years in Business')],
                    [500, '+', '', __('Products showcased')],
                    [10000, '+', '', __('Customers served')],
                    [null, '', '1:1', __('Personal consultation')],
                ] as $i => [$count, $suffix, $literal, $label])
                <div data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                    <div class="text-4xl sm:text-5xl font-black text-brand-yellow mb-2">
                        @if($count !== null)
                            <span data-count="{{ $count }}" data-suffix="{{ $suffix }}">0</span>
                        @else
                            {{ $literal }}
                        @endif
                    </div>
                    <div class="text-gray-400 text-sm">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="values-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="group bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 hover:-translate-y-1 transition-all duration-300 hover:shadow-xl" data-aos="fade-up">
                    <div class="flex justify-start mb-4 text-brand-red" aria-hidden="true">
                        <svg class="w-12 h-12 group-hover:rotate-[10deg] group-hover:scale-110 transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Our Mission') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ __('To make it easier for drivers to explore the right accessories, ask informed questions, and visit a trusted store before making a decision.') }}</p>
                </div>
                <div class="group bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 hover:-translate-y-1 transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex justify-start mb-4 text-brand-yellow" aria-hidden="true">
                        <svg class="w-12 h-12 group-hover:rotate-[10deg] group-hover:scale-110 transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path><path d="M5 3v4"></path><path d="M19 17v4"></path><path d="M3 5h4"></path><path d="M17 19h4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Our Vision') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ __('To be known as a reliable showroom and consultation brand where customers feel informed, welcomed, and confident before they buy.') }}</p>
                </div>
            </div>
        </div>
    </section>


    <section class="py-16 bg-brand-red text-white" aria-labelledby="core-values-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 id="core-values-heading" class="text-3xl sm:text-4xl font-black mb-3">{{ __('Our Core Values') }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Quality First --}}
                <div class="group bg-red-700 rounded-2xl p-6 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]" data-aos="fade-up" data-aos-delay="0">
                    <div class="flex justify-center mb-4 text-brand-yellow" aria-hidden="true">
                        <svg class="w-14 h-14 group-hover:rotate-12 group-hover:scale-110 transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ __('Quality First') }}</h3>
                    <p class="text-red-100 text-sm leading-relaxed">{{ __('We highlight products we are confident showing customers in person.') }}</p>
                </div>

                {{-- Customer Trust --}}
                <div class="group bg-red-700 rounded-2xl p-6 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex justify-center mb-4 text-brand-yellow" aria-hidden="true">
                        <svg class="w-14 h-14 group-hover:-rotate-6 group-hover:scale-110 transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m11 17 2 2a1 1 0 1 0 3-3"></path><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"></path><path d="m21 3 1 11h-1"></path><path d="M2 3 1 14l6.5 6.5a1 1 0 1 0 3-3"></path><path d="M3 4 2 3"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ __('Customer Trust') }}</h3>
                    <p class="text-red-100 text-sm leading-relaxed">{{ __('We build confidence through clear advice, real conversations, and showroom transparency.') }}</p>
                </div>

                {{-- Practical Guidance --}}
                <div class="group bg-red-700 rounded-2xl p-6 text-center hover:-translate-y-2 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex justify-center mb-4 text-brand-yellow" aria-hidden="true">
                        <svg class="w-14 h-14 group-hover:rotate-[20deg] group-hover:scale-110 transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"></path><path d="M9 18h6"></path><path d="M10 22h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ __('Practical Guidance') }}</h3>
                    <p class="text-red-100 text-sm leading-relaxed">{{ __('We focus on helping customers choose what suits their vehicle and usage.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-20" aria-labelledby="about-cta-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="relative overflow-hidden rounded-[2rem] bg-[#121212] dark:bg-[#1C1917] border border-gray-800 dark:border-gray-700 px-6 py-14 sm:px-14 sm:py-16" data-aos="fade-up">
                {{-- Contained accent glow --}}
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-red/25 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
                <div class="absolute -bottom-32 -left-16 w-80 h-80 bg-brand-red/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

                <div class="relative grid lg:grid-cols-[1.2fr_auto] gap-10 items-center text-center lg:text-left">
                    <div>
                        <h2 id="about-cta-heading" class="text-3xl sm:text-5xl text-white mb-4 leading-tight">
                            {{ __('Ready to visit or enquire?') }}
                        </h2>
                        <p class="text-white/70 text-base sm:text-lg max-w-xl mx-auto lg:mx-0">
                            {{ __('See the products online first, then continue the conversation in store or on WhatsApp.') }}
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 shrink-0 justify-center lg:justify-start items-center lg:items-stretch">
                        <x-btn.whatsapp :href="$whatsAppUrl" size="btn-lg">{{ __('WhatsApp us') }}</x-btn.whatsapp>
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
