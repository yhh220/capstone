<div>
    @php
        $storeName = config('services.store.name');
        $storePhoneRaw = config('services.store.phone_raw');
        $storeAddress = config('services.store.address');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to know more about ' . $storeName . '.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    <div class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4" data-aos="fade-up">
                {{ __('About') }} <span class="text-brand-yellow">Win Win</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                {{ __('A showroom-first brand focused on trust, product visibility, and real customer conversations.') }}
            </p>
        </div>
    </div>

    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="story-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <div class="inline-block bg-brand-red/10 dark:bg-brand-red/20 text-brand-red text-sm font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                        {{ __('Our Story') }}
                    </div>
                    <h2 id="story-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-6">
                        {{ __('We help customers choose with confidence') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                        {{ __('Win Win Car Audio Auto Accessories was built around a simple idea: customers should be able to discover products online, then speak to real people before deciding what fits their car.') }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                        {{ __('Starting from a small workshop in Kuala Lumpur, the team focused on practical advice, in-person product viewing, and honest recommendations instead of pushing quick online purchases.') }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('Today, the website works as a trusted showcase that helps customers understand the range first, then continue the conversation in the showroom or on WhatsApp.') }}
                    </p>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl h-48 sm:h-64 lg:h-80 flex items-center justify-center" role="img" aria-label="{{ __('Car illustration') }}" data-aos="fade-left" data-aos-delay="100">
                    <svg class="w-24 h-24 sm:w-32 sm:h-32 lg:w-40 lg:h-40 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 17H5a1 1 0 0 1-1-1v-5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v5a1 1 0 0 1-1 1z"></path><path d="M7 17v2"></path><path d="M17 17v2"></path><path d="M7 11V7a3 3 0 0 1 5.83-1"></path><circle cx="8" cy="14" r="1"></circle><circle cx="16" cy="14" r="1"></circle></svg>
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

    <section class="py-16 bg-white dark:bg-gray-800 text-center" aria-labelledby="about-cta-heading">
        <div class="max-w-2xl mx-auto px-4" data-aos="zoom-in">
            <h2 id="about-cta-heading" class="text-3xl font-black text-brand-black dark:text-white mb-4">
                {{ __('Ready to visit or enquire?') }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">
                {{ __('See the products online first, then continue the conversation in store or on WhatsApp.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="{{ $whatsAppUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="group relative inline-flex items-center justify-center gap-3 bg-[#25D366] text-white px-8 py-4 rounded-xl font-black text-lg transition-all duration-300 shadow-[0_6px_20px_rgba(37,211,102,0.3)] overflow-hidden hover:shadow-[0_10px_30px_rgba(37,211,102,0.45)] hover:-translate-y-1 active:scale-95">
                    <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                    <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:rotate-[15deg]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span class="relative z-10">{{ __('WhatsApp us') }}</span>
                </a>
                <a href="{{ $mapUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="group inline-flex items-center justify-center gap-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-8 py-4 rounded-xl font-black text-lg hover:border-[#C8413D] hover:text-[#C8413D] hover:bg-gray-50 dark:hover:bg-gray-800 hover:-translate-y-1 hover:shadow-md transition-all duration-300 active:scale-95">
                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    {{ __('Visit the Showroom') }}
                </a>
            </div>
        </div>
    </section>
</div>
