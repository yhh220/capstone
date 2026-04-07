<div>
    <!-- Hero -->
    <div class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-5xl font-black mb-4">About <span class="text-brand-yellow">Win Win</span></h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto">Malaysia's trusted car accessories specialist since 2015</p>
        </div>
    </div>

    <!-- Story -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-block bg-brand-red/10 text-brand-red text-sm font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4">Our Story</div>
                    <h2 class="text-4xl font-black text-brand-black mb-6">We're Passionate About Cars</h2>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Win Win Car Studio Accessories was founded in 2015 by a group of car enthusiasts who believed that every driver deserves access to premium quality car accessories at fair prices.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Starting from a small workshop in Kuala Lumpur, we've grown into one of Malaysia's most trusted names in car accessories. Our team of experts is passionate about helping customers find the perfect accessories to enhance their vehicles.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Today, we serve thousands of satisfied customers across Malaysia, offering a wide range of products from seat covers and audio systems to LED lighting and protective accessories.
                    </p>
                </div>
                <div class="bg-gray-100 rounded-2xl h-80 flex items-center justify-center text-8xl">
                    🚗
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-16 bg-brand-black text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([
                    ['10+', 'Years in Business'],
                    ['500+', 'Products'],
                    ['10,000+', 'Happy Customers'],
                    ['5★', 'Average Rating'],
                ] as [$num, $label])
                <div>
                    <div class="text-5xl font-black text-brand-yellow mb-2">{{ $num }}</div>
                    <div class="text-gray-400">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="text-5xl mb-4">🎯</div>
                    <h3 class="text-2xl font-black text-brand-black mb-4">Our Mission</h3>
                    <p class="text-gray-600 leading-relaxed">
                        To provide car enthusiasts across Malaysia with access to the highest quality accessories at competitive prices, backed by exceptional customer service and expert advice.
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="text-5xl mb-4">🌟</div>
                    <h3 class="text-2xl font-black text-brand-black mb-4">Our Vision</h3>
                    <p class="text-gray-600 leading-relaxed">
                        To become Southeast Asia's leading car accessories brand, known for our uncompromising commitment to quality, innovation, and customer satisfaction.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-brand-black mb-3">Meet Our Team</h2>
                <p class="text-gray-500">The passionate people behind Win Win Car Studio</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['Ahmad Fauzi', 'Founder & CEO', 'Car enthusiast with 15+ years in the automotive industry.'],
                    ['Nurul Ain', 'Head of Operations', 'Ensuring every order is fulfilled with precision and care.'],
                    ['Kevin Lim', 'Product Specialist', 'Expert in sourcing the best car accessories worldwide.'],
                ] as [$name, $role, $bio])
                <div class="text-center">
                    <div class="w-24 h-24 bg-brand-red text-white rounded-full flex items-center justify-center text-3xl font-black mx-auto mb-4">
                        {{ substr($name, 0, 1) }}
                    </div>
                    <h3 class="text-xl font-bold text-brand-black">{{ $name }}</h3>
                    <div class="text-brand-red text-sm font-semibold mb-2">{{ $role }}</div>
                    <p class="text-gray-500 text-sm">{{ $bio }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="py-16 bg-brand-red text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black mb-3">Our Core Values</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['🏆', 'Quality First', 'We never compromise on product quality. Every item is carefully selected and tested.'],
                    ['🤝', 'Customer Trust', 'Building long-term relationships based on honesty, transparency, and reliability.'],
                    ['💡', 'Innovation', 'Always staying ahead of trends to bring you the latest in car accessories.'],
                ] as [$icon, $title, $desc])
                <div class="bg-red-700 rounded-2xl p-6 text-center">
                    <div class="text-5xl mb-4">{{ $icon }}</div>
                    <h3 class="text-xl font-bold text-brand-yellow mb-2">{{ $title }}</h3>
                    <p class="text-red-100 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-white text-center">
        <div class="max-w-2xl mx-auto px-4">
            <h2 class="text-3xl font-black text-brand-black mb-4">Ready to Shop?</h2>
            <p class="text-gray-500 mb-8">Explore our full collection of premium car accessories.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('products') }}" class="bg-brand-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition">
                    Browse Products
                </a>
                <a href="{{ route('contact') }}" class="border-2 border-brand-black text-brand-black px-8 py-3 rounded-full font-bold hover:bg-brand-black hover:text-white transition">
                    Get in Touch
                </a>
            </div>
        </div>
    </section>
</div>
