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
                        @error('name') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pf-email" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Email') }}</label>
                        <input id="pf-email" type="email" value="{{ $email }}" disabled class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-xl px-4 py-3 text-sm bg-gray-50 cursor-not-allowed">
                        <span class="text-xs text-gray-400 mt-1">{{ __('Email cannot be changed') }}</span>
                    </div>
                    <div>
                        <label for="pf-phone" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone Number') }}</label>
                        <input wire:model="phone" id="pf-phone" autocomplete="tel" type="tel" placeholder="016-XXX XXXX" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                        @error('phone') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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

        {{-- Password — "Set" for social-login accounts with none, else "Change" --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-black text-gray-800 dark:text-white mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ auth()->user()->hasPassword() ? __('Change Password') : __('Set Password') }}
            </h2>

            @if(session('password_success'))
            <div class="flex items-center gap-2 mb-5 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('password_success') }}</p>
            </div>
            @endif

            @if(auth()->user()->hasPassword())
            {{-- Account already has a password → change it (needs the current one).
                 Passwords stay in Alpine state only — never enter the Livewire snapshot. --}}
            <form x-data="{ cp: '', np: '', npc: '', show: false }"
                  @submit.prevent="$wire.updatePassword(cp, np, npc)"
                  x-on:password-changed.window="cp = np = npc = ''"
                  class="space-y-4">
                <div>
                    <label for="pf-current-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Current Password') }}</label>
                    <div class="relative">
                        <input x-model="cp" id="pf-current-pass" :type="show ? 'text' : 'password'" autocomplete="current-password" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 pr-11 text-sm focus:outline-none focus:border-brand-red transition">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-brand-red transition-colors" :aria-label="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        </button>
                    </div>
                    @error('current_password') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pf-new-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('New Password') }}</label>
                        <div class="relative">
                            <input x-model="np" id="pf-new-pass" :type="show ? 'text' : 'password'" autocomplete="new-password" placeholder="{{ __('Min. 8 characters') }}" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 pr-11 text-sm focus:outline-none focus:border-brand-red transition">
                        </div>
                        @error('new_password') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pf-new-pass-confirm" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Confirm New Password') }}</label>
                        <div class="relative">
                            <input x-model="npc" id="pf-new-pass-confirm" :type="show ? 'text' : 'password'" autocomplete="new-password" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 pr-11 text-sm focus:outline-none focus:border-brand-red transition">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end pt-1">
                    <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                            class="bg-gray-800 dark:bg-white text-white dark:text-gray-900 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                        <span wire:loading.remove wire:target="updatePassword">{{ __('Update Password') }}</span>
                        <span wire:loading wire:target="updatePassword">{{ __('Updating...') }}</span>
                    </button>
                </div>
            </form>
            @else
            {{-- Social-login account with no password → set one (email-code gated). --}}
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">{{ __('You signed in with Google, so your account has no password yet. Set one to also log in with your email and password.') }}</p>

            @if(! $settingPassword)
                <button type="button" wire:click="sendSetPasswordCode" wire:loading.attr="disabled" wire:target="sendSetPasswordCode"
                        class="bg-brand-red text-white px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="sendSetPasswordCode">{{ __('Send verification code') }}</span>
                    <span wire:loading wire:target="sendSetPasswordCode">{{ __('Sending...') }}</span>
                </button>
                @error('set_otp') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            @else
                <form x-data="{ otp: '', np: '', npc: '', show: false }"
                      @submit.prevent="$wire.confirmSetPassword(otp, np, npc)"
                      class="space-y-4">
                    <div>
                        <label for="pf-set-otp" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Verification code') }}</label>
                        <input x-model="otp" id="pf-set-otp" type="text" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="000000"
                               x-on:input="$el.value = $el.value.replace(/\D/g, ''); otp = $el.value"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm tracking-[0.4em] focus:outline-none focus:border-brand-red transition">
                        @error('set_otp') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pf-set-new" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('New Password') }}</label>
                            <div class="relative">
                                <input x-model="np" id="pf-set-new" :type="show ? 'text' : 'password'" autocomplete="new-password" placeholder="{{ __('Min. 8 characters') }}" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 pr-11 text-sm focus:outline-none focus:border-brand-red transition">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-brand-red transition-colors" :aria-label="show ? '{{ __('Hide password') }}' : '{{ __('Show password') }}'">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                </button>
                            </div>
                            @error('set_new_password') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="pf-set-confirm" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Confirm New Password') }}</label>
                            <div class="relative">
                                <input x-model="npc" id="pf-set-confirm" :type="show ? 'text' : 'password'" autocomplete="new-password" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 pr-11 text-sm focus:outline-none focus:border-brand-red transition">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" wire:click="sendSetPasswordCode" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand-red font-semibold">{{ __('Resend code') }}</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="confirmSetPassword"
                                class="bg-gray-800 dark:bg-white text-white dark:text-gray-900 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                            <span wire:loading.remove wire:target="confirmSetPassword">{{ __('Set Password') }}</span>
                            <span wire:loading wire:target="confirmSetPassword">{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            @endif
            @endif
        </div>

        {{-- Login Verification (Email 2FA) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-black text-gray-800 dark:text-white mb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __('Login Verification') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">{{ __('When turned on, we email you a 6-digit code to enter every time you sign in, in addition to your password.') }}</p>

            @if(session('two_factor_success'))
            <div class="flex items-center gap-2 mb-5 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('two_factor_success') }}</p>
            </div>
            @endif

            @if($twoFactorEnabled)
                {{-- Enabled → offer to disable (re-enter password) --}}
                <div x-data="{ confirm: false, pw: '' }">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ __('Enabled') }}
                        </span>
                    </div>

                    <div x-show="!confirm">
                        <button type="button" @click="confirm = true"
                                class="border-2 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:border-red-400 hover:text-red-600 active:scale-95">
                            {{ __('Turn off') }}
                        </button>
                    </div>

                    <div x-show="confirm" x-cloak style="display:none;" class="space-y-4 mt-2">
                        <div>
                            <label for="pf-2fa-disable-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Enter your password to confirm') }}</label>
                            <input x-model="pw" id="pf-2fa-disable-pass" type="password" autocomplete="current-password" class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand-red transition">
                            @error('two_factor_password') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="$wire.disableTwoFactor(pw).then(() => { pw = ''; confirm = false; })"
                                    wire:loading.attr="disabled" wire:target="disableTwoFactor"
                                    class="bg-red-600 text-white px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:bg-red-700 active:scale-95 disabled:opacity-50">
                                <span wire:loading.remove wire:target="disableTwoFactor">{{ __('Turn off') }}</span>
                                <span wire:loading wire:target="disableTwoFactor">{{ __('Saving...') }}</span>
                            </button>
                            <button type="button" @click="confirm = false" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('Cancel') }}</button>
                        </div>
                    </div>
                </div>
            @elseif(! $enablingTwoFactor)
                {{-- Disabled → offer to enable (email-code gated) --}}
                <button type="button" wire:click="sendEnableTwoFactorCode" wire:loading.attr="disabled" wire:target="sendEnableTwoFactorCode"
                        class="bg-brand-red text-white px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="sendEnableTwoFactorCode">{{ __('Turn on') }}</span>
                    <span wire:loading wire:target="sendEnableTwoFactorCode">{{ __('Sending...') }}</span>
                </button>
            @else
                {{-- Awaiting confirmation code --}}
                <form x-data="{ otp: '' }" @submit.prevent="$wire.confirmEnableTwoFactor(otp)" class="space-y-4">
                    <div>
                        <label for="pf-2fa-otp" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Verification code') }}</label>
                        <input x-model="otp" id="pf-2fa-otp" type="text" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="000000"
                               x-on:input="$el.value = $el.value.replace(/\D/g, ''); otp = $el.value"
                               class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm tracking-[0.4em] focus:outline-none focus:border-brand-red transition">
                        @error('two_factor_otp') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <button type="button" wire:click="cancelEnableTwoFactor" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('Cancel') }}</button>
                        <div class="flex items-center gap-3">
                            <button type="button" wire:click="sendEnableTwoFactorCode" class="text-sm text-gray-500 dark:text-gray-400 hover:text-brand-red font-semibold">{{ __('Resend code') }}</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="confirmEnableTwoFactor"
                                    class="bg-gray-800 dark:bg-white text-white dark:text-gray-900 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                                <span wire:loading.remove wire:target="confirmEnableTwoFactor">{{ __('Confirm') }}</span>
                                <span wire:loading wire:target="confirmEnableTwoFactor">{{ __('Verifying...') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>

        {{-- Danger Zone --}}
        <div x-data="{ confirm: false, dp: '' }" class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-sm border-2 border-red-200 dark:border-red-500/30">
            <h2 class="text-lg font-black text-red-600 dark:text-red-400 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ __('Delete Account') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">{{ __('Close your account. Your order and booking history is retained for our records, and you will be signed out immediately. Signing in again later (with a password or social login) will reactivate the account.') }}</p>

            <div x-show="!confirm">
                <button type="button" @click="confirm = true"
                        class="border-2 border-red-500 text-red-600 dark:text-red-400 px-6 py-2.5 rounded-full font-bold text-sm transition-all hover:bg-red-500 hover:text-white active:scale-95">
                    {{ __('Delete My Account') }}
                </button>
            </div>

            <div x-show="confirm" x-cloak style="display:none;" class="space-y-4">
                <div>
                    <label for="pf-delete-pass" class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Enter your password to confirm') }}</label>
                    <input x-model="dp" id="pf-delete-pass" type="password" autocomplete="current-password" class="w-full border border-red-200 dark:border-red-500/40 dark:bg-gray-700 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 transition">
                    @error('delete_password') <span role="alert" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="$store.confirm.ask(@js(__('Are you sure? This will permanently close your account.')), () => $wire.deleteAccount(dp))"
                            wire:loading.attr="disabled" wire:target="deleteAccount"
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
