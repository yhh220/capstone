<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('My Profile') }}</h1>
            <p class="text-gray-400">{{ __('Update your personal details and delivery preferences') }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-10 space-y-8">

        @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
        </div>
        @endif

        <form wire:submit="updateProfile" class="space-y-6">
            {{-- Account Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-black text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ __('Account Information') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pf-name" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Name') }} *</label>
                        <input wire:model="name" id="pf-name" autocomplete="name" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition" required>
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pf-email" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email') }}</label>
                        <input id="pf-email" type="email" value="{{ $email }}" disabled class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-xl px-4 py-3 text-sm bg-gray-50 cursor-not-allowed">
                        <span class="text-xs text-gray-400 mt-1">{{ __('Email cannot be changed') }}</span>
                    </div>
                    <div>
                        <label for="pf-phone" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone Number') }}</label>
                        <input wire:model="phone" id="pf-phone" autocomplete="tel" type="tel" placeholder="016-XXX XXXX" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                        @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pf-gender" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Gender') }}</label>
                        <select wire:model="gender" id="pf-gender" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
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
                        <label for="pf-street" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Street Address') }}</label>
                        <input wire:model="addressLine" id="pf-street" autocomplete="street-address" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                    <div>
                        <label for="pf-city" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('City') }}</label>
                        <input wire:model="city" id="pf-city" autocomplete="address-level2" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                    <div>
                        <label for="pf-postcode" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Postcode') }}</label>
                        <input wire:model="postcode" id="pf-postcode" autocomplete="postal-code" type="text" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                    <div>
                        <label for="pf-state" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('State') }}</label>
                        <select wire:model="state" id="pf-state" autocomplete="address-level1" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                            <option value="">{{ __('Select State') }}</option>
                            @foreach(['Selangor','Kuala Lumpur','Johor','Penang','Perak','Pahang','Negeri Sembilan','Melaka','Kedah','Kelantan','Terengganu','Perlis','Sabah','Sarawak','Putrajaya','Labuan'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Save --}}
            <div class="flex justify-end">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="group relative overflow-hidden bg-brand-red text-white px-8 py-3 flex items-center justify-center gap-2 rounded-full font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-[0_4px_15px_rgba(232,100,96,0.4)] hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                    <span class="absolute inset-0 w-full h-full bg-white/20 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                    <span class="relative z-10" wire:loading.remove wire:target="updateProfile">{{ __('Save Changes') }}</span>
                    <span class="relative z-10 hidden" wire:loading.class.remove="hidden" wire:target="updateProfile">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>

        {{-- Change Password --}}
        <form wire:submit="updatePassword" class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-black text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('Change Password') }}
            </h2>

            @if(session('password_success'))
            <div class="flex items-center gap-2 mb-5 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('password_success') }}</p>
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="pf-current-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Current Password') }}</label>
                    <input wire:model="current_password" id="pf-current-pass" type="password" autocomplete="current-password" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    @error('current_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pf-new-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('New Password') }}</label>
                        <input wire:model="new_password" id="pf-new-pass" type="password" autocomplete="new-password" placeholder="{{ __('Min. 8 characters') }}" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                        @error('new_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pf-new-pass-confirm" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Confirm New Password') }}</label>
                        <input wire:model="new_password_confirmation" id="pf-new-pass-confirm" type="password" autocomplete="new-password" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-5">
                <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                        class="bg-gray-800 dark:bg-white text-white dark:text-gray-900 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="updatePassword">{{ __('Update Password') }}</span>
                    <span wire:loading wire:target="updatePassword">{{ __('Updating...') }}</span>
                </button>
            </div>
        </form>

        {{-- Danger Zone --}}
        <div x-data="{ confirm: false }" class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border-2 border-red-200 dark:border-red-500/30">
            <h2 class="text-lg font-black text-red-600 dark:text-red-400 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ __('Delete Account') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">{{ __('Permanently close your account. Your order and booking history is retained for our records, but you will no longer be able to sign in.') }}</p>

            <div x-show="!confirm">
                <button type="button" @click="confirm = true"
                        class="border-2 border-red-500 text-red-600 dark:text-red-400 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:bg-red-500 hover:text-white active:scale-95">
                    {{ __('Delete My Account') }}
                </button>
            </div>

            <div x-show="confirm" x-cloak style="display:none;" class="space-y-4">
                <div>
                    <label for="pf-delete-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Enter your password to confirm') }}</label>
                    <input wire:model="delete_password" id="pf-delete-pass" type="password" autocomplete="current-password" class="w-full border border-red-200 dark:border-red-500/40 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 transition">
                    @error('delete_password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="deleteAccount" wire:loading.attr="disabled" wire:target="deleteAccount"
                            wire:confirm="{{ __('Are you sure? This will permanently close your account.') }}"
                            class="bg-red-600 text-white px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:bg-red-700 active:scale-95 disabled:opacity-50">
                        <span wire:loading.remove wire:target="deleteAccount">{{ __('Permanently Delete') }}</span>
                        <span wire:loading wire:target="deleteAccount">{{ __('Deleting...') }}</span>
                    </button>
                    <button type="button" @click="confirm = false" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
