<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Book an Appointment') }}</h1>
            <p class="text-gray-400">{{ __('Fill in your details and we will confirm your appointment as soon as possible.') }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-12">
        @if(session('booking_success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-2xl p-8 text-center mb-8"
             role="alert" aria-live="polite">
            <div class="text-5xl mb-4" aria-hidden="true">✅</div>
            <h2 class="text-2xl font-black text-green-700 dark:text-green-300 mb-2">{{ __('Booking received!') }}</h2>
            <p class="text-green-600 dark:text-green-400">
                {{ __('Thank you! We will contact you on WhatsApp to confirm your appointment.') }}
            </p>
            <a href="{{ route('booking') }}"
               class="inline-block mt-6 bg-brand-red text-white px-8 py-3 rounded-full font-semibold hover:bg-red-700 transition-colors">
                {{ __('Make Another Booking') }}
            </a>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">{{ __('Appointment Details') }}</h2>

            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="booking-name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Your Name') }} <span class="text-brand-red">*</span>
                    </label>
                    <input wire:model="customer_name"
                           id="booking-name"
                           type="text"
                           placeholder="{{ __('Full name') }}"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('customer_name') border-red-400 @enderror">
                    @error('customer_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="booking-phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Phone Number') }} <span class="text-brand-red">*</span>
                    </label>
                    <input wire:model="customer_phone"
                           id="booking-phone"
                           type="tel"
                           placeholder="e.g. 012-3456789"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('customer_phone') border-red-400 @enderror">
                    @error('customer_phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="booking-email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Email') }} <span class="text-gray-400 font-normal text-xs">({{ __('optional') }})</span>
                    </label>
                    <input wire:model="customer_email"
                           id="booking-email"
                           type="email"
                           placeholder="{{ __('your@email.com') }}"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('customer_email') border-red-400 @enderror">
                    @error('customer_email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Service --}}
                <div>
                    <label for="booking-service" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Service') }} <span class="text-brand-red">*</span>
                    </label>
                    <select wire:model="service_id"
                            id="booking-service"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('service_id') border-red-400 @enderror">
                        <option value="">{{ __('Select a service...') }}</option>
                        @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->name }}{{ $svc->price ? ' — RM ' . number_format($svc->price, 2) : '' }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date + Time --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="booking-date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Preferred Date') }} <span class="text-brand-red">*</span>
                        </label>
                        <input wire:model="preferred_date"
                               id="booking-date"
                               type="date"
                               min="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('preferred_date') border-red-400 @enderror">
                        <p class="text-xs text-gray-400 mt-1">{{ __('We are closed on Fridays.') }}</p>
                        @error('preferred_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="booking-time" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Preferred Time') }} <span class="text-brand-red">*</span>
                        </label>
                        <select wire:model="preferred_time"
                                id="booking-time"
                                class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('preferred_time') border-red-400 @enderror">
                            <option value="">{{ __('Select time...') }}</option>
                            @foreach($this->availableTimes as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                        @error('preferred_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label for="booking-notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Notes') }} <span class="text-gray-400 font-normal text-xs">({{ __('optional') }})</span>
                    </label>
                    <textarea wire:model="notes"
                              id="booking-notes"
                              rows="3"
                              placeholder="{{ __('Car model, specific requirements, etc.') }}"
                              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('notes') border-red-400 @enderror"></textarea>
                    @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button wire:click="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-brand-red text-white py-3 px-8 rounded-xl font-bold text-lg hover:bg-red-700 transition-colors disabled:opacity-60">
                    <span wire:loading.remove>{{ __('Confirm Booking') }}</span>
                    <span wire:loading>{{ __('Submitting...') }}</span>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('Want to check an existing booking?') }}
            <a href="{{ route('booking.track') }}" class="text-brand-red font-semibold hover:underline ml-1">{{ __('Track Booking') }}</a>
        </div>
        @endif
    </div>
</div>
