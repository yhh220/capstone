<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Track Your Booking') }}</h1>
            <p class="text-gray-400">{{ __('Enter your booking reference and email to view or cancel your appointment.') }}</p>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 sm:p-8">
            @if(session('success'))
            <div wire:transition.opacity.duration.300ms
                 class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 rounded-xl px-4 py-3 mb-6" role="alert" aria-live="polite">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="bt-reference" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Booking Reference') }}</label>
                    <input wire:model="reference"
                           id="bt-reference"
                           wire:keydown.enter="search"
                           type="text"
                           placeholder="BK-{{ date('Y') }}-00001"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('reference') border-red-400 @enderror">
                    @error('reference') <span role="alert" class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="bt-email" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email Address') }}</label>
                    <input wire:model="email"
                           id="bt-email"
                           wire:keydown.enter="search"
                           type="email"
                           placeholder="{{ __('your@email.com') }}"
                           class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg px-4 py-2.5 focus:outline-none focus:border-brand-red transition @error('email') border-red-400 @enderror">
                    @error('email') <span role="alert" class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <button wire:click="search"
                    wire:loading.attr="disabled"
                    wire:target="search"
                    class="group relative overflow-hidden w-full bg-brand-red-solid text-white px-6 py-2.5 flex items-center justify-center gap-2 rounded-lg font-semibold transition-all duration-300 hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-0.5 active:scale-95 whitespace-nowrap disabled:opacity-60">
                <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 group-active:translate-x-0 transition-transform duration-500 ease-out"></span>
                <svg class="w-4 h-4 relative z-10" wire:loading.remove wire:target="search" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                <span class="relative z-10" wire:loading.remove wire:target="search">{{ __('Search') }}</span>
                <span class="relative z-10" wire:loading wire:target="search">{{ __('Searching...') }}</span>
            </button>

            {{-- Skeleton while searching --}}
            <div wire:loading wire:target="search" class="mt-8" aria-hidden="true">
                <div class="border border-gray-100 dark:border-gray-700 rounded-xl p-5 sm:p-6 space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="skeleton h-3 w-32"></div>
                            <div class="skeleton h-5 w-40"></div>
                        </div>
                        <div class="skeleton h-6 w-20 !rounded-full"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="skeleton h-4 w-48"></div>
                        <div class="skeleton h-3 w-36"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="skeleton h-3 w-full"></div>
                        <div class="skeleton h-3 w-full"></div>
                    </div>
                </div>
            </div>

            @if($searched && $errorMsg)
            <div class="mt-6 text-center py-8">
                <div class="flex justify-center text-gray-300 dark:text-gray-600 mb-4" aria-hidden="true">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 11H9m8 4H9m4 4H9M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path></svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400">{{ $errorMsg }}</p>
                <a href="{{ route('booking') }}" class="inline-block mt-4 text-brand-red font-semibold hover:underline">
                    {{ __('Make a Booking') }}
                </a>
            </div>
            @endif

            @if($booking)
            <div wire:transition.opacity.duration.300ms class="mt-8 border border-gray-100 dark:border-gray-700 rounded-xl p-5 sm:p-6">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">{{ __('Booking Reference') }}</div>
                        <div class="font-black text-brand-red tracking-wider">{{ $booking->reference }}</div>
                    </div>
                    <span class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        @if($booking->status === 'confirmed') bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                        @elseif($booking->status === 'cancelled') bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                        @elseif($booking->status === 'completed') bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                        @else bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300
                        @endif">
                        {{ __(ucfirst($booking->status)) }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="font-bold text-gray-800 dark:text-white">{{ $booking->service?->name ?? __('General visit') }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ optional($booking->preferred_date)->format('D, d M Y') }}{{ $booking->start_at ? ' • ' . $booking->start_at->format('h:i A') : '' }}
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <div>{{ __('Vehicle') }}: {{ $booking->vehicle_model ?: __('Not provided') }}</div>
                    <div>{{ __('Plate') }}: {{ $booking->vehicle_plate ?: __('Not provided') }}</div>
                </div>
                @if($booking->notes)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ $booking->notes }}</p>
                @endif

                @if(!in_array($booking->status, ['cancelled', 'completed'], true))
                <button type="button"
                        @click="$store.confirm.ask(@js(__('Are you sure you want to cancel this booking?')), () => $wire.cancelBooking())"
                        wire:loading.attr="disabled"
                        wire:target="cancelBooking"
                        class="group relative overflow-hidden w-full mt-5 bg-red-600 text-white py-3 rounded-xl font-semibold transition-all duration-300 hover:shadow-[0_4px_15px_rgba(220,38,38,0.4)] hover:-translate-y-0.5 active:scale-95 disabled:opacity-60">
                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 group-active:translate-x-0 transition-transform duration-500 ease-out"></span>
                    <span class="relative z-10">{{ __('Cancel Booking') }}</span>
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
