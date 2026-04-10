<div>
    {{-- ── Page header ── --}}
    <div class="bg-brand-black text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4">
                {{ __('Contact') }} <span class="text-brand-yellow">{{ __('Us') }}</span>
            </h1>
            <p class="text-gray-400">{{ __("We'd love to hear from you. Send us a message!") }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            {{-- ── Contact info column ── --}}
            <div class="space-y-6">
                <h2 class="text-2xl font-black text-brand-black dark:text-white mb-6">{{ __('Get In Touch') }}</h2>

                @foreach([
                    ['📍', __('Address'),        '123 Jalan Auto, Taman Kereta, 50000 Kuala Lumpur, Malaysia'],
                    ['📞', __('Phone'),          '+60 12-345 6789'],
                    ['✉️', __('Email'),          'info@winwincarstudio.com'],
                    ['🕐', __('Business Hours'), __('Monday – Saturday: 9:00 AM – 7:00 PM')],
                ] as [$icon, $label, $value])
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-red/10 dark:bg-brand-red/20 text-brand-red rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                         aria-hidden="true">
                        {{ $icon }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200 mb-1 text-sm">{{ $label }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $value }}</div>
                    </div>
                </div>
                @endforeach

                {{-- Social links --}}
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-3 text-sm">{{ __('Follow Us') }}</h3>
                    <div class="flex gap-3">
                        @foreach([
                            ['Facebook',  'Fb', 'https://facebook.com'],
                            ['Instagram', 'In', 'https://instagram.com'],
                            ['TikTok',    'Tk', 'https://tiktok.com'],
                        ] as [$platform, $abbr, $url])
                        <a href="{{ $url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-brand-red text-white rounded-full flex items-center justify-center text-xs font-bold hover:bg-red-700 transition-colors"
                           aria-label="Follow us on {{ $platform }}">
                            {{ $abbr }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Contact form ── --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
                    <h2 class="text-2xl font-black text-brand-black dark:text-white mb-6">{{ __('Send a Message') }}</h2>

                    @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl px-4 py-3 mb-6"
                         role="alert" aria-live="polite">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form wire:submit="submit" class="space-y-5" novalidate>

                        {{-- ── Honeypot (invisible to humans, traps bots) ── --}}
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
                                       placeholder="+60 12-345 6789"
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
                            <span wire:loading.remove>{{ __('Send Message →') }}</span>
                            <span wire:loading aria-live="polite">{{ __('Sending...') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Map placeholder ── --}}
    <div class="bg-gray-200 dark:bg-gray-700 h-64 flex items-center justify-center" aria-label="Store location map placeholder">
        <div class="text-center text-gray-500 dark:text-gray-400">
            <div class="text-5xl mb-2" aria-hidden="true">📍</div>
            <p class="font-semibold">Win Win Car Studio Accessories</p>
            <p class="text-sm">123 Jalan Auto, Kuala Lumpur, Malaysia</p>
        </div>
    </div>
</div>
