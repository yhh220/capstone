<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Track Your Booking') }}</h1>
            <p class="text-gray-400">{{ __('Enter your phone number or booking token to see appointment details.') }}</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sm:p-8">
            <div class="space-y-4 mb-8">
                <input wire:model="phone"
                       wire:keydown.enter="search"
                       type="tel"
                       placeholder="{{ __('Your phone number, e.g. 012-3456789') }}"
                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('phone') border-red-400 @enderror">

                <div class="text-center text-xs uppercase tracking-widest text-gray-400">{{ __('or') }}</div>

                <input wire:model="token"
                       wire:keydown.enter="search"
                       type="text"
                       placeholder="{{ __('Booking token') }}"
                       class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('token') border-red-400 @enderror">

                <button wire:click="search"
                        wire:loading.attr="disabled"
                        class="w-full bg-brand-red text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition-colors whitespace-nowrap disabled:opacity-60">
                    <span wire:loading.remove>{{ __('Search') }}</span>
                    <span wire:loading>...</span>
                </button>
            </div>

            @error('phone')
            <p class="text-red-500 text-sm -mt-5 mb-4">{{ $message }}</p>
            @enderror

            @error('token')
            <p class="text-red-500 text-sm -mt-5 mb-4">{{ $message }}</p>
            @enderror

            @if($searched)
                @if($this->bookings->count() > 0)
                <div class="space-y-4">
                    @foreach($this->bookings as $booking)
                    <div class="border border-gray-100 dark:border-gray-700 rounded-xl p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <div class="font-bold text-gray-800 dark:text-white">{{ $booking->service?->name ?? __('Service') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $booking->preferred_date->format('D, d M Y') }} • {{ $booking->preferred_time }}
                                </div>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($booking->status === 'confirmed') bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                                @elseif($booking->status === 'cancelled') bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                                @elseif($booking->status === 'completed') bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                                @else bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-500 dark:text-gray-400">
                            <div>{{ __('Vehicle') }}: {{ $booking->vehicle_model ?: __('Not provided') }}</div>
                            <div>{{ __('Plate') }}: {{ $booking->vehicle_plate ?: __('Not provided') }}</div>
                        </div>
                        @if($booking->notes)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ $booking->notes }}</p>
                        @endif
                        @if($booking->manage_url)
                        <a href="{{ $booking->manage_url }}" class="inline-block mt-4 text-brand-red font-semibold hover:underline">
                            {{ __('Manage this booking') }}
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="text-5xl mb-3" aria-hidden="true">?</div>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('No bookings found for the details provided.') }}</p>
                    <a href="{{ route('booking') }}" class="inline-block mt-4 text-brand-red font-semibold hover:underline">
                        {{ __('Make a Booking') }}
                    </a>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
