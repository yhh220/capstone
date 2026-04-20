<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2" data-aos="fade-up">{{ __('Our Services') }}</h1>
            <p class="text-gray-400" data-aos="fade-up" data-aos-delay="80">{{ __('Professional installation and modification services by our experienced team.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $i => $service)
            <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col hover:-translate-y-1 transition-all duration-300"
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
                <div class="h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-300 dark:text-gray-600 transition-all duration-500 overflow-hidden">
                    <svg class="w-16 h-16 group-hover:rotate-[15deg] group-hover:scale-125 group-hover:text-brand-yellow transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
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
                        @if($service->duration_label)
                        <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $service->duration_label }}
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('booking') }}?service={{ $service->id }}"
                       class="group relative block w-full bg-brand-red text-white px-4 py-3 flex items-center justify-center gap-2 rounded-xl font-bold transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-1 active:scale-95">
                        <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                        <svg class="w-5 h-5 relative z-10 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>
                        <span class="relative z-10">{{ __('Book Now') }}</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 group" data-aos="fade-up">
            <div class="flex justify-center text-gray-300 dark:text-gray-600 mb-6" aria-hidden="true">
                <svg class="w-16 h-16 group-hover:rotate-[15deg] group-hover:scale-125 group-hover:text-brand-yellow transition-all duration-500 drop-shadow-sm" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-2">{{ __('Services coming soon') }}</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ __('Check back soon or contact us on WhatsApp for more information.') }}</p>
        </div>
        @endif

        <div class="mt-12 bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white rounded-2xl p-6 sm:p-8 text-center" data-aos="zoom-in">
            <h2 class="text-2xl font-black mb-2">{{ __('Need a custom service?') }}</h2>
            <p class="text-gray-400 mb-6">{{ __('Contact us on WhatsApp and our team will advise you on the best options for your car.') }}</p>
            @php $wa = 'https://wa.me/' . config('services.store.phone_raw') . '?text=' . rawurlencode('Hi Win Win Car Studio! I would like to enquire about your services.'); @endphp
            <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer"
               class="group relative inline-flex items-center justify-center gap-2 bg-[#25D366] text-white px-8 py-3 rounded-full font-bold transition-all duration-300 overflow-hidden hover:shadow-[0_4px_15px_rgba(37,211,102,0.4)] hover:-translate-y-1 active:scale-95">
                <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                <svg class="w-5 h-5 relative z-10 group-hover:rotate-[15deg] transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span class="relative z-10">{{ __('WhatsApp Us') }}</span>
            </a>
        </div>
    </div>
</div>
