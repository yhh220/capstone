<div>
    {{-- ── Hero ── --}}
    <div class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4">
                {{ __('About') }} <span class="text-brand-yellow">Win Win</span>
            </h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                {{ __("Malaysia's trusted car accessories specialist since 2015") }}
            </p>
        </div>
    </div>

    {{-- ── Our Story ── --}}
    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="story-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-block bg-brand-red/10 dark:bg-brand-red/20 text-brand-red text-sm font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                        {{ __('Our Story') }}
                    </div>
                    <h2 id="story-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-6">
                        {{ __("We're Passionate About Cars") }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                        Win Win Car Studio Accessories was founded in 2015 by a group of car enthusiasts who believed that every driver deserves access to premium quality car accessories at fair prices.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                        Starting from a small workshop in Kuala Lumpur, we've grown into one of Malaysia's most trusted names in car accessories. Our team of experts is passionate about helping customers find the perfect accessories to enhance their vehicles.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Today, we serve thousands of satisfied customers across Malaysia, offering a wide range of products from seat covers and audio systems to LED lighting and protective accessories.
                    </p>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl h-80 flex items-center justify-center text-8xl"
                     role="img" aria-label="Car illustration">🚗</div>
            </div>
        </div>
    </section>

    {{-- ── Stats ── --}}
    <section class="py-16 bg-brand-black text-white" aria-label="Company statistics">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([
                    ['10+',    __('Years in Business')],
                    ['500+',   __('Products')],
                    ['10,000+',__('Happy Customers')],
                    ['5★',     __('Average Rating')],
                ] as [$num, $label])
                <div>
                    <div class="text-4xl sm:text-5xl font-black text-brand-yellow mb-2" aria-label="{{ $num }} {{ $label }}">{{ $num }}</div>
                    <div class="text-gray-400 text-sm">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Mission & Vision ── --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900" aria-labelledby="values-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="text-5xl mb-4" aria-hidden="true">🎯</div>
                    <h3 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Our Mission') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        To provide car enthusiasts across Malaysia with access to the highest quality accessories at competitive prices, backed by exceptional customer service and expert advice.
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="text-5xl mb-4" aria-hidden="true">🌟</div>
                    <h3 class="text-2xl font-black text-brand-black dark:text-white mb-4">{{ __('Our Vision') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        To become Southeast Asia's leading car accessories brand, known for our uncompromising commitment to quality, innovation, and customer satisfaction.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Team ── --}}
    <section class="py-16 bg-white dark:bg-gray-800" aria-labelledby="team-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="team-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white mb-3">
                    {{ __('Meet Our Team') }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400">{{ __('The passionate people behind Win Win Car Studio') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['Ahmad Fauzi', 'Founder & CEO',          'Car enthusiast with 15+ years in the automotive industry.'],
                    ['Nurul Ain',   'Head of Operations',     'Ensuring every order is fulfilled with precision and care.'],
                    ['Kevin Lim',   'Product Specialist',     'Expert in sourcing the best car accessories worldwide.'],
                ] as [$name, $role, $bio])
                <div class="text-center">
                    <div class="w-24 h-24 bg-brand-red text-white rounded-full flex items-center justify-center text-3xl font-black mx-auto mb-4"
                         aria-hidden="true">
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

    {{-- ── Core Values ── --}}
    <section class="py-16 bg-brand-red text-white" aria-labelledby="core-values-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 id="core-values-heading" class="text-3xl sm:text-4xl font-black mb-3">{{ __('Our Core Values') }}</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['🏆', __('Quality First'),    __('We never compromise on product quality. Every item is carefully selected and tested.')],
                    ['🤝', __('Customer Trust'),   __('Building long-term relationships based on honesty, transparency, and reliability.')],
                    ['💡', __('Innovation'),       __('Always staying ahead of trends to bring you the latest in car accessories.')],
                ] as [$icon, $title, $desc])
                <div class="bg-red-700 rounded-2xl p-6 text-center">
                    <div class="text-5xl mb-4" aria-hidden="true">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-red-100 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── CTA ── --}}
    <section class="py-16 bg-white dark:bg-gray-800 text-center" aria-labelledby="about-cta-heading">
        <div class="max-w-2xl mx-auto px-4">
            <h2 id="about-cta-heading" class="text-3xl font-black text-brand-black dark:text-white mb-4">
                {{ __('Ready to Shop?') }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">
                {{ __('Explore our full collection of premium car accessories.') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('products') }}"
                   class="bg-brand-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
                    {{ __('Browse Products') }}
                </a>
                <a href="{{ route('contact') }}"
                   class="border-2 border-brand-black dark:border-white text-brand-black dark:text-white px-8 py-3 rounded-full font-bold hover:bg-brand-black hover:text-white dark:hover:bg-white dark:hover:text-brand-black transition-colors">
                    {{ __('Get in Touch') }}
                </a>
            </div>
        </div>
    </section>
</div>
