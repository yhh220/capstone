<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Book an Appointment') }}</h1>
            <p class="text-gray-400">{{ __('Fill in your details and choose an available slot for your service.') }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-12">
        @if(session('booking_success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-2xl p-8 text-center mb-8"
             role="alert" aria-live="polite">
            <div class="text-5xl mb-4" aria-hidden="true">OK</div>
            <h2 class="text-2xl font-black text-green-700 dark:text-green-300 mb-2">{{ __('Booking received!') }}</h2>
            <p class="text-green-600 dark:text-green-400">
                {{ __('Thank you! Save your manage link below so you can review or cancel the booking later.') }}
            </p>
            <a href="{{ session('booking_success') }}"
               class="block mt-4 text-brand-red font-semibold break-all hover:underline">
                {{ session('booking_success') }}
            </a>
            <a href="{{ route('booking') }}"
               class="group relative inline-flex items-center gap-2 mt-6 bg-brand-red text-white px-8 py-3 rounded-full font-semibold hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-1 transition-all duration-300 active:scale-95 overflow-hidden">
                <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                <span class="relative z-10">{{ __('Make Another Booking') }}</span>
            </a>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sm:p-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">{{ __('Appointment Details') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                {{ __('Business hours: :start - :end', ['start' => $businessStart, 'end' => $businessEnd]) }}
                @if($closedDaysLabel)
                    <span class="block mt-1">{{ __('Closed on :days', ['days' => $closedDaysLabel]) }}</span>
                @endif
            </p>

            <div class="space-y-5">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
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
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="booking-vehicle-model" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Vehicle Model') }} <span class="text-brand-red">*</span>
                        </label>
                        <input wire:model="vehicle_model"
                               id="booking-vehicle-model"
                               type="text"
                               placeholder="{{ __('e.g. Myvi 1.5 AV 2022') }}"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('vehicle_model') border-red-400 @enderror">
                        @error('vehicle_model')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="booking-vehicle-plate" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Vehicle Plate') }} <span class="text-gray-400 font-normal text-xs">({{ __('optional') }})</span>
                        </label>
                        <input wire:model="vehicle_plate"
                               id="booking-vehicle-plate"
                               type="text"
                               placeholder="{{ __('e.g. ABC1234') }}"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('vehicle_plate') border-red-400 @enderror">
                        @error('vehicle_plate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="booking-service" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Service') }} <span class="text-brand-red">*</span>
                    </label>
                    <select wire:model.live="service_id"
                            id="booking-service"
                            class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('service_id') border-red-400 @enderror">
                        <option value="">{{ __('Select a service...') }}</option>
                        @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->name }}{{ $svc->price ? ' - RM ' . number_format($svc->price, 2) : '' }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="booking-date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Preferred Date') }} <span class="text-brand-red">*</span>
                        </label>
                        <input wire:model.live="preferred_date"
                               id="booking-date"
                               type="date"
                               min="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('preferred_date') border-red-400 @enderror">
                        @if($closedDaysLabel)
                        <p class="text-xs text-gray-400 mt-1">{{ __('Closed on :days.', ['days' => $closedDaysLabel]) }}</p>
                        @endif
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

                <div>
                    <label for="booking-notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Notes') }} <span class="text-gray-400 font-normal text-xs">({{ __('optional') }})</span>
                    </label>
                    <textarea wire:model="notes"
                              id="booking-notes"
                              rows="3"
                              placeholder="{{ __('Car symptoms, preferred checks, or extra requests.') }}"
                              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('notes') border-red-400 @enderror"></textarea>
                    @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="group relative overflow-hidden w-full bg-brand-red text-white py-3 px-8 flex items-center justify-center gap-2 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-0.5 active:scale-95 disabled:opacity-60">
                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                    <svg class="w-5 h-5 relative z-10 group-hover:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>
                    <span class="relative z-10" wire:loading.remove wire:target="submit">{{ __('Confirm Booking') }}</span>
                    <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="submit">{{ __('Submitting...') }}</span>
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
