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

    <div class="bg-brand-black text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4">
                {{ __('Contact') }} <span class="text-brand-yellow">{{ __('Us') }}</span>
            </h1>
            <p class="text-gray-400">{{ __('Use WhatsApp for quick questions or visit our store for a closer look at the products.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-brand-black dark:text-white mb-6">{{ __('Get In Touch') }}</h2>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-xl flex items-center justify-center text-xl flex-shrink-0" aria-hidden="true">📍</div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Address') }}</div>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed hover:text-brand-red transition-colors">{{ $storeAddress }}</a>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-xl flex items-center justify-center text-xl flex-shrink-0" aria-hidden="true">📞</div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Phone') }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $storePhoneDisplay }}</div>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-xl flex items-center justify-center text-xl flex-shrink-0" aria-hidden="true">✉</div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ __('Email') }}</div>
                        <a href="mailto:{{ $storeEmail }}" class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed hover:text-brand-red transition-colors">{{ $storeEmail }}</a>
                    </div>
                </div>

                @if($storeHours)
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-xl flex items-center justify-center text-xl flex-shrink-0" aria-hidden="true">🕐</div>
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
                           class="bg-brand-red text-white rounded-xl px-4 py-3 text-sm font-semibold text-center hover:bg-red-700 transition-colors">
                            {{ __('Chat on WhatsApp') }}
                        </a>
                        <a href="{{ $mapUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-center hover:border-brand-red hover:text-brand-red transition-colors">
                            {{ __('Get directions') }}
                        </a>
                        @if($storeFacebookUrl)
                        <a href="{{ $storeFacebookUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 text-sm font-semibold text-center hover:border-brand-red hover:text-brand-red transition-colors sm:col-span-2">
                            Facebook
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
                    <h2 class="text-2xl font-black text-brand-black dark:text-white mb-6">{{ __('Leave us a message') }}</h2>

                    @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl px-4 py-3 mb-6" role="alert" aria-live="polite">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form wire:submit="submit" class="space-y-5" novalidate>
                        <div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true" tabindex="-1">
                            <input wire:model="honeypot"
                                   type="text"
                                   name="website"
                                   autocomplete="off"
                                   tabindex="-1"
                                   placeholder="Leave this empty">
                        </div>

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
                                class="w-full bg-brand-red text-white py-4 rounded-xl font-bold text-lg hover:bg-red-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-75">
                            <span wire:loading.remove>{{ __('Send Message') }}</span>
                            <span wire:loading aria-live="polite">{{ __('Sending...') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ $mapUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="block bg-gray-200 dark:bg-gray-700 h-64">
        <div class="h-full flex items-center justify-center text-center text-gray-500 dark:text-gray-400">
            <div>
                <div class="text-5xl mb-2" aria-hidden="true">📍</div>
                <p class="font-semibold">{{ $storeName }}</p>
                <p class="text-sm">{{ $storeAddress }}</p>
            </div>
        </div>
    </a>
</div>
