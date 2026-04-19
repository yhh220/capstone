<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Manage Your Booking') }}</h1>
            <p class="text-gray-400">{{ __('Review your appointment details and current status.') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-gray-400">{{ __('Service') }}</div>
                        <div class="text-xl font-black text-gray-900 dark:text-white">{{ $booking->service?->name ?? __('Service') }}</div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-400">{{ __('Date') }}</div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ optional($booking->preferred_date)->format('D, d M Y') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-400">{{ __('Time') }}</div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $booking->preferred_time }}</div>
                        </div>
                        <div>
                            <div class="text-gray-400">{{ __('Vehicle') }}</div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $booking->vehicle_model ?: __('Not provided') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-400">{{ __('Plate Number') }}</div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $booking->vehicle_plate ?: __('Not provided') }}</div>
                        </div>
                    </div>
                    @if($booking->notes)
                    <div>
                        <div class="text-sm text-gray-400">{{ __('Notes') }}</div>
                        <div class="text-gray-700 dark:text-gray-300">{{ $booking->notes }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-sm text-gray-400">{{ __('Manage Token') }}</div>
                        <code class="text-sm text-brand-red">{{ $booking->confirm_token }}</code>
                    </div>
                </div>

                <div class="lg:w-64">
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 p-5">
                        <div class="text-sm text-gray-400 mb-2">{{ __('Status') }}</div>
                        <div class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                            @if($booking->status === 'confirmed') bg-green-100 text-green-700
                            @elseif($booking->status === 'completed') bg-blue-100 text-blue-700
                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </div>

                        @if(!in_array($booking->status, ['cancelled', 'completed'], true))
                        <button wire:click="cancelBooking"
                                class="w-full mt-5 bg-red-600 text-white py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                            {{ __('Cancel Booking') }}
                        </button>
                        @endif

                        <a href="{{ route('booking.track') }}"
                           class="block mt-3 text-center border border-brand-red text-brand-red py-3 rounded-xl font-semibold hover:bg-red-50 transition-colors">
                            {{ __('Track Other Booking') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
