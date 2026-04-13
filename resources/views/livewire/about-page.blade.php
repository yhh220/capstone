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
                        {{ $storeName }} was built around a simple idea: customers should be able to discover products online, then speak to real people before deciding what fits their car.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                        {{ __('Starting from a small workshop in Kuala Lumpur, the team focused on practical advice, in-person product viewing, and honest recommendations instead of pushing quick online purchases.') }}
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('Today, the website works as a trusted showcase that helps customers understand the range first, then continue the conversation in the showroom or on WhatsApp.') }}
                    </p>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl h-80 flex items-center justify-center text-8xl" role="img" aria-label="Car illustration" data-aos="fade-left" data-aos-delay="100">🚗</div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-brand-black text-white" aria-label="Company statistics">
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
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
                    <div class="text-5xl mb-4" aria-hidden="true">🎯</div>
                    <h3 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Our Mission') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('To make it easier for drivers to explore the right accessories, ask informed questions, and visit a trusted store before making a decision.') }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-5xl mb-4" aria-hidden="true">🌟</div>
                    <h3 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Our Vision') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('To be known as a reliable showroom and consultation brand where customers feel informed, welcomed, and confident before they buy.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="team-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 id="team-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('Meet Our Team') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">The passionate people behind {{ $storeName }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['Ahmad Fauzi', 'Founder', 'Car enthusiast with 15+ years in the automotive industry.'],
                    ['Nurul Ain', 'Customer Experience Lead', 'Helps customers coordinate showroom visits and product consultations.'],
                    ['Kevin Lim', 'Product Specialist', 'Guides customers through compatibility, features, and installation options.'],
                ] as $i => [$name, $role, $bio])
                <div class="text-center" data-aos="zoom-in" data-aos-delay="{{ $i * 120 }}">
                    <div class="w-24 h-24 bg-brand-red text-white rounded-full flex items-center justify-center text-3xl font-black mx-auto mb-4 transition-transform duration-300 hover:scale-110" aria-hidden="true">
                        {{ substr($name, 0, 1) }}
                    </div>
                    <h3 class="text-xl font-bold text-brand-black dark:text-white">{{ $name }}</h3>
                    <div class="text-brand-red text-sm font-semibold mb-2">{{ $role }}</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $bio }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 bg-brand-red text-white" aria-labelledby="core-values-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 id="core-values-heading" class="text-3xl sm:text-4xl font-black mb-3">{{ __('Our Core Values') }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['🏆', __('Quality First'), __('We highlight products we are confident showing customers in person.')],
                    ['🤝', __('Customer Trust'), __('We build confidence through clear advice, real conversations, and showroom transparency.')],
                    ['💡', __('Practical Guidance'), __('We focus on helping customers choose what suits their vehicle and usage.')],
                ] as $i => [$icon, $title, $description])
                <div class="bg-red-700 rounded-2xl p-6 text-center hover:-translate-y-1 transition-transform duration-300" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                    <div class="text-5xl mb-4" aria-hidden="true">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-red-100 text-sm leading-relaxed">{{ $description }}</p>
                </div>
                @endforeach
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
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $whatsAppUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="bg-brand-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
                    {{ __('WhatsApp us') }}
                </a>
                <a href="{{ $mapUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="border-2 border-brand-black dark:border-white text-brand-black dark:text-white px-8 py-3 rounded-full font-bold hover:bg-brand-black hover:text-white dark:hover:bg-white dark:hover:text-brand-black transition-colors">
                    {{ __('Visit the Showroom') }}
                </a>
            </div>
        </div>
    </section>
</div>
