<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2" data-aos="fade-up">{{ __('Our Services') }}</h1>
            <p class="text-gray-400" data-aos="fade-up" data-aos-delay="80">{{ __('Professional installation and modification services by our experienced team.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $i => $service)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col hover:-translate-y-1 transition-transform duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                @if($service->getImageUrl('thumb'))
                <div class="h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <img src="{{ $service->getImageUrl('thumb') }}"
                         alt="{{ $service->name }}"
                         loading="lazy"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>
                @elseif($service->image)
                <div class="h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <img src="{{ Storage::url($service->image) }}"
                         alt="{{ $service->name }}"
                         loading="lazy"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-brand-black to-gray-700 flex items-center justify-center">
                    <span class="text-6xl" aria-hidden="true">🔧</span>
                </div>
                @endif

                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-xl font-black text-gray-800 dark:text-white mb-2">{{ $service->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-4 flex-1">
                        {{ $service->description }}
                    </p>

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            @if($service->price)
                            <span class="text-brand-red font-bold text-lg">RM {{ number_format($service->price, 2) }}</span>
                            @else
                            <span class="text-gray-500 dark:text-gray-400 text-sm italic">{{ __('Contact for pricing') }}</span>
                            @endif
                        </div>
                        @if($service->duration)
                        <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $service->duration }}
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('booking') }}?service={{ $service->id }}"
                       class="block w-full bg-brand-red text-white text-center px-4 py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                        {{ __('Book Now') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20" data-aos="fade-up">
            <div class="text-6xl mb-4" aria-hidden="true">🔧</div>
            <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-2">{{ __('Services coming soon') }}</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ __('Check back soon or contact us on WhatsApp for more information.') }}</p>
        </div>
        @endif

        <div class="mt-12 bg-brand-black text-white rounded-2xl p-6 sm:p-8 text-center" data-aos="zoom-in">
            <h2 class="text-2xl font-black mb-2">{{ __('Need a custom service?') }}</h2>
            <p class="text-gray-400 mb-6">{{ __('Contact us on WhatsApp and our team will advise you on the best options for your car.') }}</p>
            @php $wa = 'https://wa.me/' . config('services.store.phone_raw') . '?text=' . rawurlencode('Hi Win Win Car Studio! I would like to enquire about your services.'); @endphp
            <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer"
               class="inline-block bg-brand-yellow text-brand-black px-8 py-3 rounded-full font-bold hover:bg-yellow-300 transition-colors">
                {{ __('WhatsApp Us') }}
            </a>
        </div>
    </div>
</div>
