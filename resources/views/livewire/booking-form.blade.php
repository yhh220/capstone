<div>
    {{-- ── PAGE HEADER ── --}}
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Book Your Appointment') }}</h1>
            <p class="text-gray-400">{{ __('Select your service, pick a slot, and we will handle the rest.') }}</p>
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
            <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Thank you! Save the manage link below to review or cancel your booking later.') }}</p>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">{{ __('Your Booking Link') }}</p>
                <a href="{{ $manageUrl }}" class="text-brand-red font-semibold text-sm break-all hover:underline">{{ $manageUrl }}</a>
            </div>
            <button wire:click="$set('submitted', false)"
                    class="group relative inline-flex items-center gap-3 bg-brand-red text-white px-8 py-4 rounded-full font-black text-base transition-all duration-300 shadow-[0_6px_20px_rgba(var(--brand-red-rgb),0.35)] overflow-hidden hover:shadow-[0_10px_30px_rgba(var(--brand-red-rgb),0.5)] hover:-translate-y-2 active:scale-95">
                <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                <svg class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:rotate-[15deg]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                <span class="relative z-10">{{ __('Make Another Booking') }}</span>
            </button>
        </div>
    </div>

    {{-- ── MULTI-STEP BOOKING WIZARD ── --}}
    @else {{-- !$submitted --}}
    <div class="max-w-7xl mx-auto px-4 py-10 sm:py-14">
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- ── LEFT: PROGRESS SIDEBAR ── --}}
            <div class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 lg:sticky lg:top-6">

                    {{-- Step tracker --}}
                    <p class="text-xs font-black uppercase tracking-[0.15em] text-gray-400 mb-5">
                        {{ __('Step :current of :total', ['current' => $currentStep, 'total' => $totalSteps]) }}
                    </p>

                    <div class="space-y-1">
                        @php
                            $steps = [
                                1 => ['label' => __('Service'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>'],
                                2 => ['label' => __('Date & Time'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>'],
                                3 => ['label' => __('Vehicle'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>'],
                                4 => ['label' => __('Confirm'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>'],
                            ];
                        @endphp

                        @foreach($steps as $num => $step)
                        <button wire:click="goToStep({{ $num }})"
                                @if($num >= $currentStep) disabled @endif
                                class="w-full flex items-center gap-3.5 p-3 rounded-xl transition-all duration-200 group
                                    {{ $num === $currentStep ? 'bg-brand-red/10 text-brand-red' : ($num < $currentStep ? 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer' : 'text-gray-300 dark:text-gray-600 cursor-default') }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-black transition-all duration-200
                                {{ $num === $currentStep ? 'bg-brand-red text-white shadow-[0_4px_12px_rgba(220,38,38,0.35)]' : ($num < $currentStep ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400') }}">
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
                                @if($num === 1 && $selectedService && $currentStep > 1)
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $selectedService->name }}</p>
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

                    {{-- Summary card (visible after step 1) --}}
                    @if($selectedService)
                    <div class="mt-5 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <p class="text-xs font-black uppercase tracking-wider text-gray-400 mb-3">{{ __('Summary') }}</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Service') }}</span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 text-right">{{ $selectedService->name }}</span>
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
                            @if($selectedService->price)
                            <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center">
                                <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Est. Price') }}</span>
                                <span class="text-base font-black text-brand-red">RM {{ number_format($selectedService->price, 2) }}</span>
                            </div>
                            @else
                            <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center">
                                <span class="text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Est. Price') }}</span>
                                <span class="text-sm font-black text-brand-yellow">{{ __('Quote-based') }}</span>
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
            </div>

            {{-- ── RIGHT: STEP CONTENT ── --}}
            <div class="flex-1 min-w-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

                    {{-- Step header stripe --}}
                    <div class="px-6 sm:px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                        @php
                            $stepTitles = [
                                1 => ['title' => __('Select Your Service'), 'sub' => __('Choose the service that best fits your needs.')],
                                2 => ['title' => __('Pick a Date & Time'), 'sub' => __('Business hours: :start – :end', ['start' => $businessStart, 'end' => $businessEnd]) . ($closedDaysLabel ? '. ' . __('Closed: :days', ['days' => $closedDaysLabel]) : '')],
                                3 => ['title' => __('Vehicle Details'), 'sub' => __('Tell us about the car we will be working on.')],
                                4 => ['title' => __('Your Details & Confirm'), 'sub' => __('Almost done — fill in your contact info and submit.')],
                            ];
                        @endphp
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">{{ $stepTitles[$currentStep]['title'] }}</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $stepTitles[$currentStep]['sub'] }}</p>
                    </div>

                    <div class="px-6 sm:px-8 py-7">

                        {{-- ══ STEP 1: SERVICE SELECTION ══ --}}
                        @if($currentStep === 1)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($services as $svc)
                            <button wire:click="$set('service_id', '{{ $svc->id }}')"
                                    type="button"
                                    class="group relative text-left p-5 rounded-xl border-2 transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]
                                        {{ $service_id == $svc->id
                                            ? 'border-brand-red bg-brand-red/5 dark:bg-brand-red/10 shadow-[0_4px_20px_rgba(220,38,38,0.15)]'
                                            : 'border-gray-100 dark:border-gray-700 hover:border-brand-red/40 bg-white dark:bg-gray-800' }}">

                                {{-- Selected badge --}}
                                @if($service_id == $svc->id)
                                <div class="absolute top-3 right-3 w-5 h-5 bg-brand-red rounded-full flex items-center justify-center shadow">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                @endif

                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-lg mb-3 flex items-center justify-center
                                    {{ $service_id == $svc->id ? 'bg-brand-red text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 group-hover:bg-brand-red/10 group-hover:text-brand-red' }}
                                    transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
                                    </svg>
                                </div>

                                <p class="font-black text-gray-900 dark:text-white text-sm leading-tight mb-1">{{ $svc->name }}</p>

                                @if($svc->description)
                                <p class="text-xs text-gray-400 leading-relaxed mb-3 line-clamp-2">{{ $svc->description }}</p>
                                @endif

                                @if($svc->price)
                                <span class="inline-flex items-center gap-1 text-xs font-black px-2.5 py-1 rounded-full
                                    {{ $service_id == $svc->id ? 'bg-brand-red text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    RM {{ number_format($svc->price, 2) }}
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-xs font-black px-2.5 py-1 rounded-full
                                    {{ $service_id == $svc->id ? 'bg-brand-yellow/20 text-yellow-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                    {{ __('Quote-based') }}
                                </span>
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
                                <label for="booking-date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Preferred Date') }} <span class="text-brand-red">*</span>
                                </label>
                                <input wire:model.live="preferred_date"
                                       id="booking-date"
                                       type="date"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full sm:w-64 border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('preferred_date') border-red-400 @enderror">
                                @if($closedDaysLabel)
                                <p class="text-xs text-gray-400 mt-1.5">{{ __('Closed on :days.', ['days' => $closedDaysLabel]) }}</p>
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
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('Available Time Slots') }} <span class="text-brand-red">*</span>
                                </label>
                                @if(count($this->availableTimes) > 0)
                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($this->availableTimes as $time)
                                    <button wire:click="$set('preferred_time', '{{ $time }}')"
                                            type="button"
                                            class="px-4 py-2 rounded-xl text-sm font-bold border-2 transition-all duration-150 active:scale-95
                                                {{ $preferred_time === $time
                                                    ? 'border-brand-red bg-brand-red text-white shadow-[0_4px_12px_rgba(220,38,38,0.3)]'
                                                    : 'border-gray-100 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-brand-red/50 hover:text-brand-red' }}">
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
                                    {{ __('Vehicle Model') }} <span class="text-brand-red">*</span>
                                </label>
                                <input wire:model="vehicle_model"
                                       id="booking-vehicle-model"
                                       type="text"
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
                                          placeholder="{{ __('Describe any symptoms, preferred products, or special requests...') }}"
                                          class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm resize-none @error('notes') border-red-400 @enderror"></textarea>
                                @error('notes')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>


                        {{-- ══ STEP 4: CONTACT & CONFIRM ══ --}}
                        @elseif($currentStep === 4)
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                            {{-- Contact fields --}}
                            <div class="lg:col-span-3 space-y-5">
                                <div>
                                    <label for="booking-name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                        {{ __('Your Name') }} <span class="text-brand-red">*</span>
                                    </label>
                                    <input wire:model="customer_name"
                                           id="booking-name"
                                           type="text"
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
                                        {{ __('Email') }}
                                        <span class="text-gray-400 font-normal text-xs ml-1">({{ __('optional') }})</span>
                                    </label>
                                    <input wire:model="customer_email"
                                           id="booking-email"
                                           type="email"
                                           placeholder="{{ __('your@email.com') }}"
                                           class="w-full border-2 border-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-brand-red transition-colors text-sm @error('customer_email') border-red-400 @enderror">
                                    @error('customer_email')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Booking summary panel --}}
                            <div class="lg:col-span-2">
                                <div class="bg-gray-900 dark:bg-gray-700/60 rounded-2xl p-5 text-white">
                                    <p class="text-xs font-black uppercase tracking-[0.15em] text-gray-400 mb-4">{{ __('Booking Summary') }}</p>

                                    <div class="space-y-3">
                                        @if($selectedService)
                                        <div class="flex gap-3 items-start pb-3 border-b border-white/10">
                                            <div class="w-8 h-8 bg-brand-red/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Service') }}</p>
                                                <p class="text-sm font-bold text-white">{{ $selectedService->name }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($preferred_date && $preferred_time)
                                        <div class="flex gap-3 items-start pb-3 border-b border-white/10">
                                            <div class="w-8 h-8 bg-brand-red/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Date & Time') }}</p>
                                                <p class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($preferred_date)->format('d M Y') }}</p>
                                                <p class="text-xs text-gray-400">{{ $preferred_time }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($vehicle_model)
                                        <div class="flex gap-3 items-start pb-3 border-b border-white/10">
                                            <div class="w-8 h-8 bg-brand-red/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ __('Vehicle') }}</p>
                                                <p class="text-sm font-bold text-white">{{ $vehicle_model }}</p>
                                                @if($vehicle_plate)
                                                <p class="text-xs text-gray-400 font-mono uppercase">{{ $vehicle_plate }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-xs font-black uppercase tracking-wider text-gray-400">{{ __('Est. Total') }}</span>
                                            @if($selectedService?->price)
                                            <span class="text-xl font-black text-brand-red">RM {{ number_format($selectedService->price, 2) }}</span>
                                            @else
                                            <span class="text-sm font-black text-brand-yellow">{{ __('Quote after visit') }}</span>
                                            @endif
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
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 dark:text-gray-400 border-2 border-gray-100 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200 active:scale-95">
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
                                class="group relative inline-flex items-center gap-2 px-7 py-3 bg-brand-red text-white rounded-xl text-sm font-black transition-all duration-300 shadow-[0_4px_15px_rgba(var(--brand-red-rgb),0.3)] overflow-hidden hover:shadow-[0_8px_25px_rgba(var(--brand-red-rgb),0.45)] hover:-translate-y-1 active:scale-95 disabled:opacity-60">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
                            <span class="relative z-10" wire:loading.remove wire:target="nextStep">{{ __('Continue') }}</span>
                            <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="nextStep">{{ __('Checking...') }}</span>
                            <svg class="w-4 h-4 relative z-10 transition-transform duration-300 group-hover:translate-x-1" wire:loading.remove wire:target="nextStep" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @else
                        <button wire:click="submit"
                                wire:loading.attr="disabled"
                                wire:target="submit"
                                type="button"
                                class="group relative inline-flex items-center gap-2 px-8 py-3 bg-brand-red text-white rounded-xl font-black text-sm transition-all duration-300 shadow-[0_4px_15px_rgba(var(--brand-red-rgb),0.3)] overflow-hidden hover:shadow-[0_8px_25px_rgba(var(--brand-red-rgb),0.45)] hover:-translate-y-1 active:scale-95 disabled:opacity-60">
                            <span class="absolute inset-0 bg-white/25 skew-x-[45deg] -translate-x-full group-hover:translate-x-[150%] transition-transform duration-700 ease-out" aria-hidden="true"></span>
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
