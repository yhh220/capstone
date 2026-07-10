<div class="relative min-h-screen overflow-hidden bg-gray-50 dark:bg-[#0C0C0E]">

    {{-- ── Ambient background layers ───────────────────────────── --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -top-40 -left-40 w-[42rem] h-[42rem] rounded-full opacity-60 dark:opacity-100 auth-glow"
             style="background: radial-gradient(circle, rgba(200,65,61,0.22) 0%, transparent 65%);"></div>
        <div class="absolute -bottom-52 right-[-10rem] w-[40rem] h-[40rem] rounded-full opacity-50"
             style="background: radial-gradient(circle, rgba(200,65,61,0.10) 0%, transparent 65%);"></div>
        <div class="absolute inset-0 auth-grid opacity-[0.5] dark:opacity-[0.7]"></div>
    </div>

    <div class="relative z-10 flex items-center justify-center min-h-screen px-5 py-12 sm:px-8">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="mb-8 flex justify-center">
                <a href="{{ route('home') }}" wire:navigate class="inline-block">
                    <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="Win Win Car Audio" class="h-8 w-auto dark:hidden">
                    <img src="{{ asset('images/logo/logo-light.svg') }}" alt="Win Win Car Audio" class="h-8 w-auto hidden dark:block">
                </a>
            </div>

            {{-- Glass card --}}
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white/80 dark:bg-white/[0.03] backdrop-blur-xl shadow-2xl shadow-black/5 dark:shadow-black/40 p-6 sm:p-8">

                {{-- ============ STEP 1 · REQUEST CODE ============ --}}
                @if($step === 1)
                <div wire:key="reset-step-1">
                    <h1 class="font-display font-black text-3xl uppercase text-gray-900 dark:text-white mb-1">{{ __('Forgot Password') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-white/40 mb-7">{{ __('Enter your email and we will send you a code to reset it.') }}</p>

                    <form wire:submit="sendCode" class="space-y-5">
                        <x-honeypot livewire-model="honeypotData" />
                        <div>
                            <label for="reset-email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Email') }}</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <input wire:model="email" type="email" id="reset-email" placeholder="your@email.com" autocomplete="email"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                            </div>
                            @error('email')
                                <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="sendCode"
                                class="btn btn-primary btn-shine w-full !py-3.5 !rounded-xl uppercase tracking-widest font-black text-sm">
                            <span wire:loading.remove wire:target="sendCode" class="inline-flex items-center gap-2">
                                {{ __('Send code') }}
                                <svg class="icon-sm icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </span>
                            <span wire:loading.inline-flex wire:target="sendCode" class="inline-flex items-center gap-2">
                                <svg class="icon-sm icon-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                {{ __('Sending...') }}
                            </span>
                        </button>
                    </form>
                </div>

                {{-- ============ STEP 2 · ENTER CODE + NEW PASSWORD ============ --}}
                @elseif($step === 2)
                <div wire:key="reset-step-2">
                    <button type="button" wire:click="backToEmail"
                            class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-white/40 hover:text-brand-red transition-colors mb-6">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        {{ __('Back') }}
                    </button>

                    <h1 class="font-display font-black text-3xl uppercase text-gray-900 dark:text-white mb-1">{{ __('Reset Password') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-white/40 mb-6">
                        {{ __('Enter the code sent to') }}
                        <span class="font-bold text-gray-700 dark:text-white/70 break-all">{{ $email }}</span>
                    </p>

                    @if(session('reset_sent'))
                    <div class="flex items-center gap-2 mb-5 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
                        <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ session('reset_sent') }}</p>
                    </div>
                    @endif

                    {{-- Password/confirmation stay in Alpine state only — never enter the
                         Livewire snapshot. Passed to resetPassword() as method arguments. --}}
                    <form x-data="{ password: '', password_confirmation: '' }" @submit.prevent="$wire.resetPassword(password, password_confirmation)" class="space-y-5">
                        {{-- Code --}}
                        <div>
                            <label for="reset-otp" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Verification Code') }}</label>
                            <input wire:model="otpCode" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" id="reset-otp" placeholder="••••••"
                                   class="w-full text-center font-mono text-2xl tracking-[0.6em] py-3.5 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-white/20 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                            @error('otpCode')
                                <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- New password --}}
                        <div>
                            <label for="reset-password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('New Password') }}</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input x-model="password" type="{{ $showPassword ? 'text' : 'password' }}" id="reset-password" placeholder="{{ __('Min. 8 characters') }}" autocomplete="new-password"
                                       class="w-full pl-11 pr-12 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                                <button type="button" wire:click="$toggle('showPassword')" aria-label="{{ $showPassword ? __('Hide password') : __('Show password') }}"
                                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 hover:text-brand-red transition-colors">
                                    @if($showPassword)
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                    @else
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    @endif
                                </button>
                            </div>
                            @error('password')
                                <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Confirm password --}}
                        <div>
                            <label for="reset-password-confirm" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Confirm Password') }}</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input x-model="password_confirmation" type="{{ $showPassword ? 'text' : 'password' }}" id="reset-password-confirm" placeholder="{{ __('Re-enter password') }}" autocomplete="new-password"
                                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                            </div>
                            @error('password_confirmation')
                                <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="resetPassword"
                                class="btn btn-primary btn-shine w-full !py-3.5 !rounded-xl uppercase tracking-widest font-black text-sm">
                            <span wire:loading.remove wire:target="resetPassword" class="inline-flex items-center gap-2">
                                {{ __('Reset Password') }}
                                <svg class="icon-sm icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </span>
                            <span wire:loading.inline-flex wire:target="resetPassword" class="inline-flex items-center gap-2">
                                <svg class="icon-sm icon-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                {{ __('Resetting...') }}
                            </span>
                        </button>
                    </form>

                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-500 dark:text-white/40">
                            {{ __("Didn't get the code?") }}
                            <button type="button" wire:click="resendCode" wire:loading.attr="disabled" wire:target="resendCode"
                                    class="font-semibold text-brand-red hover:underline disabled:opacity-50">{{ __('Resend code') }}</button>
                        </p>
                    </div>
                </div>

                {{-- ============ STEP 3 · DONE ============ --}}
                @else
                <div wire:key="reset-step-3" class="text-center py-4">
                    <div class="mx-auto mb-6 flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-500/15 text-green-500">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h1 class="font-display font-black text-2xl uppercase text-gray-900 dark:text-white mb-2">{{ __('Password Reset!') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-white/40 mb-7">{{ __('Your password has been updated. You can now sign in with your new password.') }}</p>
                    <a href="{{ route('login') }}" wire:navigate
                       class="btn btn-primary btn-shine w-full !py-3.5 !rounded-xl uppercase tracking-widest font-black text-sm inline-flex items-center justify-center gap-2">
                        {{ __('Sign In') }}
                        <svg class="icon-sm icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
                @endif

                {{-- Back to login --}}
                @if($step !== 3)
                <div class="flex items-center justify-center gap-2 mt-7 pt-5 border-t border-gray-100 dark:border-white/10">
                    <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-gray-500 dark:text-white/45 hover:text-brand-red transition-colors">
                        &larr; {{ __('Back to Sign In') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Scoped styles (mirrors the sign-in page) --}}
    <style>
        .auth-grid {
            background-image:
                linear-gradient(rgba(120,120,130,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(120,120,130,0.06) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, #000 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, #000 30%, transparent 80%);
        }
        @keyframes authGlowPulse { 0%,100% { opacity: .55; transform: scale(1); } 50% { opacity: .9; transform: scale(1.06); } }
        .auth-glow { animation: authGlowPulse 7s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { .auth-glow { animation: none; } }
    </style>
</div>
