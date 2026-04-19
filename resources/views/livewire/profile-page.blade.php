<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('My Profile') }}</h1>
            <p class="text-gray-400">{{ __('Update your personal details and delivery preferences') }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-10">
        <form wire:submit="updateProfile" class="space-y-6">
            {{-- Account Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-black text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('Account Information') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Name') }} *</label>
                        <input wire:model="name" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email') }}</label>
                        <input type="email" value="{{ $email }}" disabled class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-xl px-4 py-3 text-sm bg-gray-50 cursor-not-allowed">
                        <span class="text-xs text-gray-400 mt-1">{{ __('Email cannot be changed') }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone Number') }}</label>
                        <input wire:model="phone" type="tel" placeholder="016-XXX XXXX" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                        @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Gender') }}</label>
                        <select wire:model="gender" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                            <option value="">{{ __('Select') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                            <option value="female">{{ __('Female') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-black text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Delivery Address') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Street Address') }}</label>
                        <input wire:model="addressLine" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('City') }}</label>
                        <input wire:model="city" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Postcode') }}</label>
                        <input wire:model="postcode" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('State') }}</label>
                        <select wire:model="state" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                            <option value="">{{ __('Select State') }}</option>
                            @foreach(['Selangor','Kuala Lumpur','Johor','Penang','Perak','Pahang','Negeri Sembilan','Melaka','Kedah','Kelantan','Terengganu','Perlis','Sabah','Sarawak','Putrajaya','Labuan'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Preferred Courier --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-black text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    {{ __('Preferred Courier Service') }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    {{-- Pos Laju --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="courier" value="poslaju" class="sr-only peer">
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all
                                    peer-checked:border-brand-red peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:shadow-md
                                    border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500">
                            <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white font-black text-xs shadow-sm">
                                <svg viewBox="0 0 24 24" class="w-7 h-7" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Pos Laju</span>
                        </div>
                    </label>

                    {{-- DHL --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="courier" value="dhl" class="sr-only peer">
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all
                                    peer-checked:border-brand-red peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:shadow-md
                                    border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500">
                            <div class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center font-black text-red-600 text-sm shadow-sm">
                                DHL
                            </div>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">DHL</span>
                        </div>
                    </label>

                    {{-- Ninja Van --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="courier" value="ninjavan" class="sr-only peer">
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all
                                    peer-checked:border-brand-red peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:shadow-md
                                    border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500">
                            <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center text-white shadow-sm">
                                <svg viewBox="0 0 24 24" class="w-7 h-7" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Ninja Van</span>
                        </div>
                    </label>

                    {{-- GDEX --}}
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="courier" value="gdex" class="sr-only peer">
                        <div class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all
                                    peer-checked:border-brand-red peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:shadow-md
                                    border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500">
                            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-black text-xs shadow-sm">
                                GDEX
                            </div>
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">GDEX</span>
                        </div>
                    </label>
                </div>
                @error('courier') <span class="text-red-500 text-xs mt-2">{{ $message }}</span> @enderror
            </div>

            {{-- Save --}}
            <div class="flex justify-end">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="bg-brand-red text-white px-8 py-3 rounded-full font-bold text-lg hover:bg-red-700 transition-all duration-300 shadow-lg hover:shadow-red-500/20 hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="updateProfile">{{ __('Save Changes') }}</span>
                    <span wire:loading wire:target="updateProfile">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
