<div>
    @php
        // Per-service icons — same mapping as the Services page so the icons match.
        $serviceIcons = [
            'audio' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
            'subwoofer' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="4"></circle><path d="M12 3v2M12 19v2M3 12h2M19 12h2"></path></svg>',
            'tint' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M7 9h10M7 13h6"></path></svg>',
            'camera' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3Z"></path><circle cx="12" cy="13" r="3"></circle></svg>',
            'security' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="m9 12 2 2 4-5"></path></svg>',
            'tuning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3"></path><path d="M2 14h4M10 8h4M18 16h4"></path></svg>',
            'default' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9l-3.8 3.8Z"></path></svg>',
        ];
        $iconFor = function (string $name) use ($serviceIcons): string {
            $needle = strtolower($name);
            return match (true) {
                str_contains($needle, 'subwoofer'), str_contains($needle, 'amplifier') => $serviceIcons['subwoofer'],
                str_contains($needle, 'tint') => $serviceIcons['tint'],
                str_contains($needle, 'dashcam'), str_contains($needle, 'camera') => $serviceIcons['camera'],
                str_contains($needle, 'alarm'), str_contains($needle, 'security') => $serviceIcons['security'],
                str_contains($needle, 'dsp'), str_contains($needle, 'tuning'), str_contains($needle, 'calibration') => $serviceIcons['tuning'],
                str_contains($needle, 'audio'), str_contains($needle, 'speaker') => $serviceIcons['audio'],
                default => $serviceIcons['default'],
            };
        };
    @endphp
    {{-- ── PAGE HEADER ── --}}
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-4xl sm:text-5xl font-black mb-4">{{ __('Book Your Appointment') }}</h1>
            <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">{{ __('Pick a date and time to drop by our showroom — we will see you there.') }}</p>
        </div>
    </div>

    {{-- ── SUCCESS STATE ── --}}
    @if($submitted)
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl p-10">
            <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">{{ __('Booking Confirmed!') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Thank you! Save your booking reference below. To check or cancel your booking later, use it together with your phone number.') }}</p>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">{{ __('Your Booking Reference') }}</p>
                <p class="text-2xl font-black text-brand-red tracking-wider select-all">{{ $reference }}</p>
            </div>
            <a href="{{ route('booking.track') }}" class="block text-brand-red font-semibold text-sm mb-6 hover:underline">
                {{ __('Track or cancel this booking') }} <span aria-hidden="true">&rarr;</span>
            </a>
            <button wire:click="$set('submitted', false)"
                    class="group relative inline-flex items-center gap-3 bg-brand-red-solid text-white px-8 py-4 rounded-full font-black text-base transition-all duration-300 shadow-[0_6px_20px_rgb(var(--brand-red-rgb)_/_0.35)] overflow-hidden hover:shadow-[0_10px_30px_rgb(var(--brand-red-rgb)_/_0.5)] hover:-translate-y-2 active:scale-95">
                <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] group-active:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:rotate-[15deg]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                <span class="relative z-10">{{ __('Make Another Booking') }}</span>
            </button>
        </div>
    </div>

    {{-- ── MULTI-STEP BOOKING WIZARD ── --}}
    @else {{-- !$submitted --}}
    @php
        $steps = [
            1 => ['label' => __('About'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>'],
            2 => ['label' => __('Date & Time'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>'],
            3 => ['label' => __('Vehicle'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>'],
            4 => ['label' => __('Confirm'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>'],
        ];
    @endphp
    <x-honeypot livewire-model="honeypotData" />
    <div class="max-w-7xl mx-auto px-4 py-10 sm:py-14 scroll-mt-24"
         x-data
         x-on:booking-step.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">
        <div class="flex flex-col md:flex-row gap-6 items-start">

            {{-- ── LEFT: PROGRESS SIDEBAR ── --}}
            <div class="w-full md:w-56 lg:w-72 flex-shrink-0">
                {{-- Desktop Vertical Tracker --}}
                <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 lg:sticky lg:top-6">
                    <p class="text-xs font-black uppercase tracking-[0.15em] text-gray-400 mb-5">
                        {{ __('Step :current of :total', ['current' => $currentStep, 'total' => $totalSteps]) }}
                    </p>

                    <div class="space-y-1">
                        @foreach($steps as $num => $step)
                        <button wire:click="goToStep({{ $num }})"
                                @if($num >= $currentStep) disabled @endif
                                class="w-full flex items-center gap-3.5 p-3 rounded-xl transition-all duration-200 group
                                    {{ $num === $currentStep ? 'bg-brand-red/10 text-brand-red' : ($num < $currentStep ? 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer' : 'text-gray-300 dark:text-gray-600 cursor-default') }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-black transition-all duration-200
                                {{ $num === $currentStep ? 'bg-brand-red-solid text-white shadow-[0_4px_12px_rgba(220,38,38,0.35)]' : ($num < $currentStep ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400') }}">
                                @if($num < $currentStep)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $num }}
                                @endif
                            </div>
                            <div class="text-left min-w-0">
                                <p class="text-xs font-black uppercase tracking-wider {{ $num === $currentStep ? 'text-brand-red' : '' }}">
                                    {{ $step['label'] }}
                                </p>
                                @if($num === 1 && $currentStep > 1)
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $selectedService?->name ?? __('General visit') }}</p>
                                @elseif($num === 2 && $preferred_date && $preferred_time && $currentStep > 2)
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ \Carbon\Carbon::parse($preferred_date)->format('d M') }} · {{ $preferred_time }}</p>
                                @elseif($num === 3 && $vehicle_model && $currentStep > 3)
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $vehicle_model }}</p>
                                @endif
                            </div>
                        </button>
                        @endforeach
                    </div>

                    {{-- Progress bar --}}
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-xs font-semibold text-gray-400 mb-2">
                            <span>{{ __('Progress') }}</span>
                            <span>{{ round((($currentStep - 1) / $totalSteps) * 100) }}%</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-red rounded-full transition-all duration-500 ease-out"
                                 style="width: {{ round((($currentStep - 1) / $totalSteps) * 100) }}%"></div>
                        </div>
                    </div>

                    {{-- Summary card --}}
                    @if($selectedService || $preferred_date || $preferred_time)
                    <div class="mt-5 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <p class="text-xs font-black uppercase tracking-wider text-gray-400 mb-3">{{ __('Summary') }}</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('About') }}</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 text-right">{{ $selectedService?->name ?? __('General visit') }}</span>
                            </div>
                            @if($preferred_date)
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Date') }}</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($preferred_date)->format('d M Y') }}</span>
                            </div>
                            @endif
                            @if($preferred_time)
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Time') }}</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $preferred_time }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Track booking link --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-400 text-center">
                            {{ __('Existing booking?') }}
                            <a href="{{ route('booking.track') }}" class="text-brand-red font-semibold hover:underline ml-1">{{ __('Track it') }}</a>
                        </p>
                    </div>
                </div>

                {{-- Mobile/Tablet Compact Tracker --}}
                <div class="lg:hidden bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 w-full">
                    <div class="flex items-center justify-between text-xs font-black uppercase tracking-wider text-gray-800 dark:text-gray-200 mb-2">
                        <span>{{ __('Step :current of :total', ['current' => $currentStep, 'total' => $totalSteps]) }}: {{ $steps[$currentStep]['label'] }}</span>
                        <span class="text-brand-red">{{ round((($currentStep - 1) / $totalSteps) * 100) }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-red rounded-full transition-all duration-500 ease-out"
                             style="width: {{ round((($currentStep - 1) / $totalSteps) * 100) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT: STEP CONTENT ── --}}
            <div class="flex-1 min-w-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                    {{-- Step header stripe --}}
                    <div class="px-6 sm:px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                        @php
                            $stepTitles = [
                                1 => ['title' => __('What would you like to discuss?'), 'sub' => __('Optional — pick what your visit is about, or just choose a general visit.')],
                                2 => ['title' => __('Pick a Date & Time'), 'sub' => __('Business hours: :start – :end', ['start' => $businessStart, 'end' => $businessEnd]) . ($closedDaysLabel ? '. ' . __('Closed: :days', ['days' => $closedDaysLabel]) : '')],
                                3 => ['title' => __('Vehicle Details'), 'sub' => __('Tell us about your car so we can prepare for your visit.')],
                                4 => ['title' => __('Your Details & Confirm'), 'sub' => __('Almost done — fill in your contact info and submit.')],
                            ];
                        @endphp
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">{{ $stepTitles[$currentStep]['title'] }}</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $stepTitles[$currentStep]['sub'] }}</p>
                    </div>

                    <div class="px-6 sm:px-8 py-7">

                        {{-- ══ STEP 1: WHAT'S THE VISIT ABOUT (optional) ══ --}}
                        @if($currentStep === 1)
                        {{-- selected is entangled with service_id so the card highlight + badge
                             apply instantly on tap instead of waiting on a wire:click round-trip
                             (service_id has no side effects beyond the raw value, same as the
                             time-slot fix below). --}}
                        <div x-data="{ selected: $wire.entangle('service_id') }" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- General visit — the default, no specific service --}}
                            <button @click="selected = ''"
                                    type="button"
                                    class="group relative text-left p-5 rounded-xl border-2 transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]"
                                    :class="selected === ''
                                        ? 'border-brand-red bg-brand-red/5 dark:bg-brand-red/10 shadow-[0_4px_20px_rgba(220,38,38,0.15)]'
                                        : 'border-gray-100 dark:border-gray-700 hover:border-brand-red/40 bg-white dark:bg-gray-800'">
                                <div x-show="selected === ''" class="absolute top-3 right-3 w-5 h-5 bg-brand-red-solid rounded-full flex items-center justify-center shadow">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="w-10 h-10 rounded-lg mb-3 flex items-center justify-center transition-all duration-200"
                                     :class="selected === '' ? 'bg-brand-red-solid text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-brand-red/10 group-hover:text-brand-red'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </div>
                                <p class="font-black text-gray-900 dark:text-white text-sm leading-tight mb-1">{{ __('General visit') }}</p>
                                <p class="text-xs text-gray-400 leading-relaxed">{{ __('Just coming in to look around or have a chat.') }}</p>
                            </button>

                            @foreach($services as $svc)
                            <button @click="selected = '{{ $svc->id }}'"
                                    type="button"
                                    class="group relative text-left p-5 rounded-xl border-2 transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]"
                                    :class="selected == '{{ $svc->id }}'
                                        ? 'border-brand-red bg-brand-red/5 dark:bg-brand-red/10 shadow-[0_4px_20px_rgba(220,38,38,0.15)]'
                                        : 'border-gray-100 dark:border-gray-700 hover:border-brand-red/40 bg-white dark:bg-gray-800'">

                                {{-- Selected badge --}}
                                <div x-show="selected == '{{ $svc->id }}'" class="absolute top-3 right-3 w-5 h-5 bg-brand-red-solid rounded-full flex items-center justify-center shadow">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>

                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-lg mb-3 flex items-center justify-center transition-all duration-200"
                                     :class="selected == '{{ $svc->id }}' ? 'bg-brand-red text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-brand-red/10 group-hover:text-brand-red'">
                                    {!! str_replace('w-6 h-6', 'w-5 h-5', $iconFor($svc->name)) !!}
                                </div>

                                <p class="font-black text-gray-900 dark:text-white text-sm leading-tight mb-1">{{ $svc->name }}</p>

                                @if($svc->description)
                                <p class="text-xs text-gray-400 leading-relaxed line-clamp-2">{{ $svc->description }}</p>
                                @endif
                            </button>
                            @endforeach
                        </div>
                        @error('service_id')
                        <p class="text-red-500 text-xs mt-3 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                        @enderror


                        {{-- ══ STEP 2: DATE & TIME ══ --}}
                        @elseif($currentStep === 2)
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Preferred Date') }} <span class="text-brand-red">*</span>
                                </label>
                                <p class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    {{ __('You can book any date within the next :months months.', ['months' => $this->maxMonthsAhead]) }}
                                </p>

                                {{-- Inline month calendar (server-rendered, business-rule aware) --}}
                                <div class="max-w-sm border-2 border-gray-100 dark:border-gray-600 rounded-2xl p-4 bg-white dark:bg-gray-800 @error('preferred_date') !border-red-400 @enderror">
                                    {{-- Month nav --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <button type="button" wire:click="prevMonth" @disabled(!$this->canGoPrevMonth)
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                                aria-label="{{ __('Previous month') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                                        </button>
                                        <div class="text-sm font-black text-gray-800 dark:text-white">{{ $this->calendarLabel }}</div>
                                        <button type="button" wire:click="nextMonth" @disabled(!$this->canGoNextMonth)
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                                aria-label="{{ __('Next month') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                                        </button>
                                    </div>

                                    {{-- Weekday headers (Mon-first) --}}
                                    <div class="grid grid-cols-7 gap-1 mb-1">
                                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $wd)
                                        <div class="text-center text-[10px] font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 py-1">{{ __($wd) }}</div>
                                        @endforeach
                                    </div>

                                    {{-- Day grid. selectedDate is entangled with preferred_date so the
                                         tapped day highlights instantly; wire:click still fires
                                         selectDate() for its real server-side validation (past/closed/
                                         out-of-range) and the preferred_time reset side effect. --}}
                                    <div x-data="{ selectedDate: $wire.entangle('preferred_date') }" class="grid grid-cols-7 gap-1">
                                        @foreach($this->calendarDays as $cell)
                                            @if($cell['selectable'])
                                            <button type="button" @click="selectedDate = '{{ $cell['date'] }}'" wire:click="selectDate('{{ $cell['date'] }}')"
                                                    class="aspect-square flex items-center justify-center rounded-lg text-sm font-semibold transition-all active:scale-90"
                                                    :class="selectedDate === '{{ $cell['date'] }}'
                                                        ? 'bg-brand-red-solid text-white shadow-[0_4px_12px_rgba(200,65,61,0.35)]'
                                                        : 'text-gray-700 dark:text-gray-200 hover:bg-brand-red/10 hover:text-brand-red {{ $cell['isToday'] ? 'ring-1 ring-brand-red/40' : '' }}'">
                                                {{ $cell['day'] }}
                                            </button>
                                            @else
                                            <div class="aspect-square flex items-center justify-center rounded-lg text-sm {{ $cell['inMonth'] ? 'text-gray-300 dark:text-gray-600' : 'text-transparent' }}"
                                                 @if($cell['inMonth'] && $cell['isClosed']) title="{{ __('Closed') }}" @endif>
                                                {{ $cell['inMonth'] ? $cell['day'] : '' }}
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                @if($closedDaysLabel)
                                <p class="text-xs text-gray-400 mt-2">{{ __('Closed on :days.', ['days' => $closedDaysLabel]) }}</p>
                                @endif
                                @error('preferred_date')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            @if($preferred_date)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                    {{ __('Available Time Slots') }} <span class="text-brand-red">*</span>
                                    <svg wire:loading wire:target="selectDate, prevMonth, nextMonth" class="w-4 h-4 text-brand-red animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </label>
                                @if(count($this->availableTimes) > 0)
                                {{-- selected is entangled with preferred_time (not a disconnected
                                     Alpine var) so the highlight applies instantly on tap and stays
                                     correct across re-renders — picking a slot used to wait on a
                                     full wire:click round-trip before it visually registered. --}}
                                <div x-data="{ selected: $wire.entangle('preferred_time') }" class="flex flex-wrap gap-2.5">
                                    @foreach($this->availableTimes as $time)
                                    <button @click="selected = '{{ $time }}'"
                                            type="button"
                                            class="px-4 py-2 rounded-xl text-sm font-bold border-2 transition-all duration-150 active:scale-95"
                                            :class="selected === '{{ $time }}'
                                                ? 'border-brand-red bg-brand-red-solid text-white shadow-[0_4px_12px_rgba(220,38,38,0.3)]'
                                                : 'border-gray-100 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-red/50 hover:text-brand-red'">
                                        {{ $time }}
                                    </button>
                                    @endforeach
                                </div>
                                @else
                                <div class="flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/40 rounded-xl">
                                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                    <p class="text-sm text-amber-700 dark:text-amber-300">{{ __('No available slots for this date. Please choose another day.') }}</p>
                                </div>
                                @endif
                                @error('preferred_time')
                                <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            @else
                            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-600">
                                <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm text-gray-400">{{ __('Select a date first to see available slots.') }}</p>
                            </div>
                            @endif
                        </div>


                        {{-- ══ STEP 3: VEHICLE ══ --}}
                        @elseif($currentStep === 3)
                        <div class="space-y-5 max-w-lg">
                            <div>
                                <label for="booking-vehicle-model" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Vehicle Model') }}
                                    @if($service_id !== '')
                                        <span class="text-brand-red">*</span>
                                    @else
                                        <span class="text-gray-400 font-normal text-xs ml-1">({{ __('optional') }})</span>
                                    @endif
                                </label>
                                <input wire:model="vehicle_model"
                                       id="booking-vehicle-model"
                                       type="text"
                                       maxlength="120"
                                       placeholder="{{ __('e.g. Perodua Myvi 1.5 AV 2022') }}"
                                       class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('vehicle_model') border-red-400 @enderror">
                                @error('vehicle_model')
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label for="booking-vehicle-plate" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Vehicle Plate') }}
                                    <span class="text-gray-400 font-normal text-xs ml-1">({{ __('optional') }})</span>
                                </label>
                                <input wire:model="vehicle_plate"
                                       id="booking-vehicle-plate"
                                       type="text"
                                       maxlength="30"
                                       placeholder="{{ __('e.g. ABC 1234') }}"
                                       class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('vehicle_plate') border-red-400 @enderror">
                                @error('vehicle_plate')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="booking-notes" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Notes') }}
                                    <span class="text-gray-400 font-normal text-xs ml-1">({{ __('optional') }})</span>
                                </label>
                                <textarea wire:model="notes"
                                          id="booking-notes"
                                          rows="4"
                                          maxlength="1000"
                                          placeholder="{{ __('Describe any symptoms, preferred products, or special requests...') }}"
                                          class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm resize-none @error('notes') border-red-400 @enderror"></textarea>
                                @error('notes')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>


                        {{-- ══ STEP 4: CONTACT & CONFIRM ══ --}}
                        @elseif($currentStep === 4)
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                            {{-- Contact fields --}}
                            <div class="md:col-span-3 space-y-5">
                                <div>
                                    <label for="booking-name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Your Name') }} <span class="text-brand-red">*</span>
                                    </label>
                                    <input wire:model="customer_name"
                                           id="booking-name"
                                           type="text"
                                           maxlength="100"
                                           placeholder="{{ __('Full name') }}"
                                           class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('customer_name') border-red-400 @enderror">
                                    @error('customer_name')
                                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="booking-phone" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Phone Number') }} <span class="text-brand-red">*</span>
                                    </label>
                                    <input wire:model="customer_phone"
                                           id="booking-phone"
                                           type="tel"
                                           maxlength="20"
                                           placeholder="e.g. 012-3456789"
                                           class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('customer_phone') border-red-400 @enderror">
                                    @error('customer_phone')
                                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="booking-email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Email') }} <span class="text-brand-red">*</span>
                                    </label>
                                    <input wire:model="customer_email"
                                           id="booking-email"
                                           type="email"
                                           maxlength="100"
                                           placeholder="{{ __('your@email.com') }}"
                                           class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('customer_email') border-red-400 @enderror">
                                    @error('customer_email')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Booking summary panel --}}
                            <div class="md:col-span-2">
                                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5">
                                    <p class="text-xs font-black uppercase tracking-[0.15em] text-gray-400 mb-4">{{ __('Booking Summary') }}</p>

                                    <div class="space-y-3">
                                        @if($selectedService)
                                        <div class="flex gap-3 items-start pb-3 border-b border-gray-100 dark:border-white/10">
                                            <div class="w-8 h-8 bg-brand-red/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Service') }}</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $selectedService->name }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($preferred_date && $preferred_time)
                                        <div class="flex gap-3 items-start pb-3 border-b border-gray-100 dark:border-white/10">
                                            <div class="w-8 h-8 bg-brand-red/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Date & Time') }}</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($preferred_date)->format('d M Y') }}</p>
                                                <p class="text-xs text-gray-400">{{ $preferred_time }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($vehicle_model)
                                        <div class="flex gap-3 items-start pb-3 border-b border-gray-100 dark:border-white/10">
                                            <div class="w-8 h-8 bg-brand-red/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Vehicle') }}</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $vehicle_model }}</p>
                                                @if($vehicle_plate)
                                                <p class="text-xs text-gray-400 font-mono uppercase">{{ $vehicle_plate }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-xs font-black uppercase tracking-wider text-gray-400">{{ __('About') }}</span>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white text-right">{{ $selectedService?->name ?? __('General visit') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- ── NAVIGATION FOOTER ── --}}
                    <div class="px-6 sm:px-8 py-5 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4">
                        @if($currentStep > 1)
                        <button wire:click="prevStep"
                                type="button"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 dark:text-gray-400 border-2 border-gray-100 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200 active:scale-95 shrink-0 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            {{ __('Back') }}
                        </button>
                        @else
                        <div></div>
                        @endif

                        @if($currentStep < $totalSteps)
                        <button wire:click="nextStep"
                                wire:loading.attr="disabled"
                                wire:target="nextStep"
                                type="button"
                                class="group relative inline-flex items-center gap-2 px-7 py-3 bg-brand-red-solid text-white rounded-xl text-sm font-black transition-all duration-300 shadow-[0_4px_15px_rgb(var(--brand-red-rgb)_/_0.3)] overflow-hidden hover:shadow-[0_8px_25px_rgb(var(--brand-red-rgb)_/_0.45)] hover:-translate-y-1 active:scale-95 disabled:opacity-60 shrink-0 whitespace-nowrap">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] group-active:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <span class="relative z-10" wire:loading.remove wire:target="nextStep">{{ __('Continue') }}</span>
                            <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="nextStep">{{ __('Checking...') }}</span>
                            <svg class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover:translate-x-1" wire:loading.remove wire:target="nextStep" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @else
                        <button wire:click="submit"
                                wire:loading.attr="disabled"
                                wire:target="submit"
                                type="button"
                                class="group relative inline-flex items-center gap-2 px-8 py-3 bg-brand-red-solid text-white rounded-xl font-black text-sm transition-all duration-300 shadow-[0_4px_15px_rgb(var(--brand-red-rgb)_/_0.3)] overflow-hidden hover:shadow-[0_8px_25px_rgb(var(--brand-red-rgb)_/_0.45)] hover:-translate-y-1 active:scale-95 disabled:opacity-60">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] group-active:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <svg class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover:scale-110" wire:loading.remove wire:target="submit" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="relative z-10" wire:loading.remove wire:target="submit">{{ __('Confirm Booking') }}</span>
                            <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="submit">{{ __('Submitting...') }}</span>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Trust badges --}}
                <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach([
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01.06 12.45c.01.531.048 1.059.14 1.58C1.666 19.116 6.37 22.5 12 22.5s10.334-3.384 11.8-8.47c.092-.521.13-1.049.14-1.58A11.955 11.955 0 0120.402 6a11.959 11.959 0 01-5.402-5.036z"/>', 'label' => __('Verified Technicians')],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'label' => __('Free Cancellation')],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>', 'label' => __('WhatsApp Confirmation')],
                    ] as $badge)
                    <div class="flex items-center gap-2.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 px-4 py-3">
                        <svg class="w-4 h-4 text-brand-red flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">{!! $badge['icon'] !!}</svg>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $badge['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
