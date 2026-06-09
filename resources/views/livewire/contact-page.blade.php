<div>
    @php
        $storeName = config('services.store.name');
        $storePhoneDisplay = config('services.store.phone_display');
        $storePhoneRaw = config('services.store.phone_raw');
        $storeEmail = config('services.store.email');
        $storeFacebookUrl = config('services.store.facebook_url');
        $storeAddress = config('services.store.address');
        $storeHours = config('services.store.hours');
        $whatsAppUrl = 'https://wa.me/' . $storePhoneRaw . '?text=' . rawurlencode('Hello, I would like to contact ' . $storeName . '.');
        $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($storeAddress);
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4" data-aos="fade-up">
                {{ __('Contact') }} <span class="text-brand-yellow">{{ __('Us') }}</span>
            </h1>
            <p class="text-gray-400" data-aos="fade-up" data-aos-delay="100">{{ __('Use WhatsApp for quick questions or visit our store for a closer look at the products.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <div class="space-y-6" data-aos="fade-right">
                <h2 class="text-2xl font-black text-brand-black dark:text-white mb-6">{{ __('Get In Touch') }}</h2>

                <div class="flex items-start gap-4 group">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 text-brand-black dark:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:bg-brand-red group-hover:text-white group-hover:shadow-[0_4px_15px_rgba(232,100,96,0.3)] group-hover:-translate-y-1" aria-hidden="true">
                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Address') }}</div>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed hover:text-brand-red transition-colors">{{ $storeAddress }}</a>
                    </div>
                </div>
                <div class="flex items-start gap-4 group">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 text-brand-black dark:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:bg-brand-red group-hover:text-white group-hover:shadow-[0_4px_15px_rgba(232,100,96,0.3)] group-hover:-translate-y-1" aria-hidden="true">
                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Phone') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $storePhoneDisplay }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-4 group">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 text-brand-black dark:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:bg-brand-red group-hover:text-white group-hover:shadow-[0_4px_15px_rgba(232,100,96,0.3)] group-hover:-translate-y-1" aria-hidden="true">
                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:-rotate-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Email') }}</div>
                        <a href="mailto:{{ $storeEmail }}" class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed hover:text-brand-red transition-colors">{{ $storeEmail }}</a>
                    </div>
                </div>
                @if($storeHours)
                <div class="flex items-start gap-4 group">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 text-brand-black dark:text-white rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:bg-brand-red group-hover:text-white group-hover:shadow-[0_4px_15px_rgba(232,100,96,0.3)] group-hover:-translate-y-1" aria-hidden="true">
                        <svg class="w-6 h-6 transition-transform duration-300 group-hover:rotate-45" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Business Hours') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $storeHours }}</div>
                    </div>
                </div>
                @endif

                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-3 text-sm">{{ __('Quick actions') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ $whatsAppUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group relative inline-flex items-center justify-center gap-2 bg-[#25D366] text-white rounded-xl px-4 py-3 text-sm font-bold overflow-hidden hover:shadow-[0_4px_15px_rgba(37,211,102,0.4)] hover:-translate-y-1 transition-all duration-300 active:scale-95">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover:rotate-[15deg]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span class="relative z-10">{{ __('Chat on WhatsApp') }}</span>
                        </a>
                        <a href="{{ $mapUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group inline-flex items-center justify-center gap-2 border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm font-bold hover:border-brand-red hover:text-brand-red hover:bg-gray-50 dark:hover:bg-gray-800 hover:-translate-y-1 hover:shadow-md transition-all duration-300 active:scale-95">
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-125" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            {{ __('Get directions') }}
                        </a>
                        @if($storeFacebookUrl)
                        <a href="{{ $storeFacebookUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group sm:col-span-2 relative inline-flex items-center justify-center gap-2 bg-[#1877F2] text-white rounded-xl px-4 py-3 text-sm font-bold overflow-hidden hover:shadow-[0_4px_15px_rgba(24,119,242,0.4)] hover:-translate-y-1 transition-all duration-300 active:scale-95">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            <span class="relative z-10">Facebook</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2" data-aos="fade-left" data-aos-delay="100">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
                    <h2 class="text-2xl font-black text-brand-black dark:text-white mb-6">{{ __('Leave us a message') }}</h2>

                    @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl px-4 py-3 mb-6" role="alert" aria-live="polite">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form wire:submit="submit" class="space-y-5" novalidate>
                        <x-honeypot livewire-model="honeypotData" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="contact-name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('Full Name *') }}
                                </label>
                                <input wire:model="name"
                                       id="contact-name"
                                       type="text"
                                       placeholder="{{ __('Your name') }}"
                                       autocomplete="name"
                                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition placeholder-gray-400 dark:placeholder-gray-500">
                                @error('name') <span class="text-red-500 text-xs mt-1 block" role="alert">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="contact-email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('Email Address *') }}
                                </label>
                                <input wire:model="email"
                                       id="contact-email"
                                       type="email"
                                       placeholder="{{ __('your@email.com') }}"
                                       autocomplete="email"
                                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition placeholder-gray-400 dark:placeholder-gray-500">
                                @error('email') <span class="text-red-500 text-xs mt-1 block" role="alert">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="contact-phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('Phone Number') }}
                                </label>
                                <input wire:model="phone"
                                       id="contact-phone"
                                       type="tel"
                                       placeholder="{{ $storePhoneDisplay }}"
                                       autocomplete="tel"
                                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition placeholder-gray-400 dark:placeholder-gray-500">
                                @error('phone') <span class="text-red-500 text-xs mt-1 block" role="alert">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="contact-subject" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('Subject *') }}
                                </label>
                                <input wire:model="subject"
                                       id="contact-subject"
                                       type="text"
                                       placeholder="{{ __('How can we help?') }}"
                                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition placeholder-gray-400 dark:placeholder-gray-500">
                                @error('subject') <span class="text-red-500 text-xs mt-1 block" role="alert">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="contact-message" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('Message *') }}
                            </label>
                            <textarea wire:model="message"
                                      id="contact-message"
                                      rows="5"
                                      placeholder="{{ __('Write your message here...') }}"
                                      class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red/30 focus:border-brand-red transition resize-none placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1 block" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit"
                                class="group relative overflow-hidden w-full bg-brand-red text-white py-4 flex items-center justify-center gap-3 rounded-xl font-black text-lg transition-all duration-300 shadow-[0_6px_20px_rgb(var(--brand-red-rgb)_/_0.35)] hover:shadow-[0_10px_30px_rgb(var(--brand-red-rgb)_/_0.5)] hover:-translate-y-1 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75"
                                wire:target="submit">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:translate-x-1" wire:loading.remove wire:target="submit" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13"></path><path d="m22 2-7 20-4-9-9-4 20-7z"></path></svg>
                            <span class="relative z-10" wire:loading.remove wire:target="submit">{{ __('Send Message') }}</span>
                            <svg class="w-5 h-5 relative z-10 icon-spin hidden" wire:loading.class.remove="hidden" wire:target="submit" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="submit" aria-live="polite">{{ __('Sending...') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section class="py-16 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700" aria-labelledby="contact-location-heading">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-10" data-aos="fade-up">
                <div class="inline-block bg-brand-red/10 dark:bg-brand-red/20 text-brand-red text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-3">
                    {{ __('Find Us') }}
                </div>
                <h2 id="contact-location-heading" class="text-3xl sm:text-4xl font-black text-brand-black dark:text-white">
                    {{ __('Visit Our Showroom') }}
                </h2>
            </div>

            <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg" data-aos="fade-up">
                <div id="store-map" style="height: clamp(240px, 50vw, 420px); width: 100%;"></div>
            </div>

            @push('scripts')
            <script>
            (function() {
                function initMap() {
                    var el = document.getElementById('store-map');
                    if (!el || el._leaflet_id) return;
                    var lat = 3.1491, lng = 101.5465;
                    var map = L.map('store-map', { zoomControl: true, scrollWheelZoom: false }).setView([lat, lng], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        maxZoom: 19
                    }).addTo(map);
                    var icon = L.divIcon({
                        html: '<div style="background:#C8413D;width:14px;height:14px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.4);"></div>',
                        iconSize: [14, 14], iconAnchor: [7, 7], className: ''
                    });
                    L.marker([lat, lng], { icon: icon })
                        .addTo(map)
                        .bindPopup('<strong>Win Win Car Audio</strong><br>No. 22, Jalan Dinar C U3/C<br>Taman Subang Perdana, Shah Alam')
                        .openPopup();
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initMap);
                } else {
                    initMap();
                }
                document.addEventListener('livewire:navigated', function() {
                    setTimeout(initMap, 100);
                });
            })();
            </script>
            @endpush
        </div>
    </section>
</div>
