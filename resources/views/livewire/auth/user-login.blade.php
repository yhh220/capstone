<div class="relative min-h-screen overflow-hidden bg-gray-50 dark:bg-[#0C0C0E]">

    {{-- ── Ambient background layers ───────────────────────────── --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        {{-- red radial glow --}}
        <div class="absolute -top-40 -left-40 w-[42rem] h-[42rem] rounded-full opacity-60 dark:opacity-100 auth-glow"
             style="background: radial-gradient(circle, rgba(200,65,61,0.22) 0%, transparent 65%);"></div>
        <div class="absolute -bottom-52 right-[-10rem] w-[40rem] h-[40rem] rounded-full opacity-50"
             style="background: radial-gradient(circle, rgba(200,65,61,0.10) 0%, transparent 65%);"></div>
        {{-- subtle grid texture --}}
        <div class="absolute inset-0 auth-grid opacity-[0.5] dark:opacity-[0.7]"></div>
    </div>

    <div class="relative z-10 grid lg:grid-cols-2 min-h-screen">

        {{-- ════════ LEFT · BRAND PANEL ════════ --}}
        <div class="hidden lg:flex flex-col justify-center p-12 xl:p-16 relative overflow-hidden">
            {{-- thin red accent line down the divider edge --}}
            <div class="absolute top-0 right-0 h-full w-px bg-gradient-to-b from-transparent via-brand-red/40 to-transparent"></div>

            {{-- Centred brand block --}}
            <div class="relative z-10 max-w-md">
                <h2 class="font-display font-black text-[clamp(2.6rem,3.6vw,3.6rem)] leading-[0.92] uppercase text-white mb-8">
                    {{ __("Shah Alam's") }}<br>
                    <span class="text-brand-red">{{ __('Car Audio') }}</span><br>
                    {{ __('Specialist.') }}
                </h2>

                <div class="space-y-3.5">
                    @foreach([__('Curated brands & products'), __('Expert installation'), __('Walk-in showroom welcome')] as $point)
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-red/15 text-brand-red shrink-0">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <span class="text-sm text-white/70">{{ $point }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Stats --}}
                <div class="flex gap-8 items-center mt-12 pt-10 border-t border-white/10">
                    <div>
                        <div class="font-display font-black text-3xl text-white">1000+</div>
                        <div class="font-mono text-[10px] tracking-widest uppercase text-white/35 mt-0.5">{{ __('Installations') }}</div>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div>
                        <div class="font-display font-black text-3xl text-white">20+</div>
                        <div class="font-mono text-[10px] tracking-widest uppercase text-white/35 mt-0.5">{{ __('Brands') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════ RIGHT · FORM PANEL ════════ --}}
        <div class="flex items-center justify-center px-5 py-12 sm:px-8 lg:px-10">
            <div class="w-full max-w-md">

                {{-- Mobile logo --}}
                <div class="lg:hidden mb-8 flex justify-center">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="Win Win Car Audio" class="h-8 w-auto dark:hidden">
                        <img src="{{ asset('images/logo/logo-light.svg') }}" alt="Win Win Car Audio" class="h-8 w-auto hidden dark:block">
                    </a>
                </div>

                {{-- Glass card --}}
                <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white/80 dark:bg-white/[0.03] backdrop-blur-xl shadow-2xl shadow-black/5 dark:shadow-black/40 p-6 sm:p-8">

                    {{-- Segmented sliding tab --}}
                    <div class="relative grid grid-cols-2 p-1 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 mb-8">
                        <div class="absolute top-1 bottom-1 left-1 rounded-lg bg-brand-red transition-transform duration-300 ease-out"
                             style="width: calc(50% - 0.25rem); box-shadow: 0 4px 14px rgba(200,65,61,0.45); transform: translateX({{ $isLoginTab ? '0%' : '100%' }});" aria-hidden="true"></div>
                        <button type="button" wire:click="switchTab(true)"
                                class="relative z-10 py-2.5 text-sm font-black uppercase tracking-widest transition-colors duration-200 {{ $isLoginTab ? 'text-white' : 'text-gray-500 dark:text-white/45 hover:text-gray-800 dark:hover:text-white/70' }}">
                            {{ __('Sign In') }}
                        </button>
                        <button type="button" wire:click="switchTab(false)"
                                class="relative z-10 py-2.5 text-sm font-black uppercase tracking-widest transition-colors duration-200 {{ !$isLoginTab ? 'text-white' : 'text-gray-500 dark:text-white/45 hover:text-gray-800 dark:hover:text-white/70' }}">
                            {{ __('Register') }}
                        </button>
                    </div>

                    {{-- ============ SIGN IN ============ --}}
                    @if($isLoginTab)
                    <div wire:key="login-form">
                        <h1 class="font-display font-black text-3xl uppercase text-gray-900 dark:text-white mb-1">{{ __('Welcome Back') }}</h1>
                        <p class="text-sm text-gray-500 dark:text-white/40 mb-7">{{ __('Sign in to your account') }}</p>

                        <form wire:submit="login" class="space-y-5">

                            {{-- Email --}}
                            <div>
                                <label for="login-email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Email') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </span>
                                    <input wire:model="loginEmail" type="email" id="login-email" placeholder="your@email.com" autocomplete="email"
                                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                                </div>
                                @error('loginEmail')
                                    <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="login-password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Password') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input wire:model="loginPassword" type="{{ $showPassword ? 'text' : 'password' }}" id="login-password" placeholder="••••••••" autocomplete="current-password"
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
                                @error('loginPassword')
                                    <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Remember me --}}
                            <label for="remember" class="flex items-center gap-2.5 cursor-pointer select-none w-fit">
                                <input wire:model="remember" type="checkbox" id="remember" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 bg-transparent accent-brand-red">
                                <span class="text-sm text-gray-500 dark:text-white/45">{{ __('Remember me') }}</span>
                            </label>

                            {{-- Submit — site primary button --}}
                            <button type="submit" wire:loading.attr="disabled" wire:target="login"
                                    class="btn btn-primary btn-shine w-full !py-3.5 !rounded-xl uppercase tracking-widest font-black text-sm">
                                <span wire:loading.remove wire:target="login" class="inline-flex items-center gap-2">
                                    {{ __('Sign In') }}
                                    <svg class="icon-sm icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </span>
                                <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                                    <svg class="icon-sm icon-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    {{ __('Signing in...') }}
                                </span>
                            </button>
                        </form>
                    </div>

                    {{-- ============ REGISTER ============ --}}
                    @else
                    <div wire:key="register-form">
                        <h1 class="font-display font-black text-3xl uppercase text-gray-900 dark:text-white mb-1">{{ __('Create Account') }}</h1>
                        <p class="text-sm text-gray-500 dark:text-white/40 mb-7">{{ __('Join the Win Win community') }}</p>

                        <form wire:submit="register" class="space-y-5">
                            <x-honeypot livewire-model="honeypotData" />

                            {{-- Full Name --}}
                            <div>
                                <label for="reg-name" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Full Name') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </span>
                                    <input wire:model="name" type="text" id="reg-name" placeholder="{{ __('Your full name') }}" autocomplete="name"
                                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                                </div>
                                @error('name')
                                    <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="reg-email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Email') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </span>
                                    <input wire:model="email" type="email" id="reg-email" placeholder="your@email.com" autocomplete="email"
                                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                                </div>
                                @error('email')
                                    <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="reg-password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Password') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input wire:model.live="password" type="{{ $showPassword ? 'text' : 'password' }}" id="reg-password" placeholder="{{ __('Min. 8 characters') }}" autocomplete="new-password"
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

                                {{-- Password strength: 5 segment bars --}}
                                @if(strlen($password) > 0)
                                @php
                                    $strength = 0;
                                    if (strlen($password) >= 8) $strength++;
                                    if (preg_match('/[A-Z]/', $password)) $strength++;
                                    if (preg_match('/[a-z]/', $password)) $strength++;
                                    if (preg_match('/[0-9]/', $password)) $strength++;
                                    if (preg_match('/[^A-Za-z0-9]/', $password)) $strength++;
                                    $strengthColorMap = ['bg-red-500','bg-red-400','bg-orange-400','bg-green-500','bg-emerald-500'];
                                    $strengthLabels   = [__('Very Weak'),__('Weak'),__('Medium'),__('Strong'),__('Very Strong')];
                                    $activeColor = $strengthColorMap[max(0, $strength - 1)];
                                @endphp
                                <div class="mt-2.5">
                                    <div class="flex gap-1 mb-1.5">
                                        @for($s = 1; $s <= 5; $s++)
                                        <div class="flex-1 h-1 rounded-full transition-colors duration-300 {{ $s <= $strength ? $activeColor : 'bg-gray-200 dark:bg-white/10' }}"></div>
                                        @endfor
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400 dark:text-white/35">{{ $strengthLabels[max(0, $strength - 1)] }}</p>
                                        <div class="flex gap-2 text-xs font-bold">
                                            <span class="{{ strlen($password) >= 8 ? 'text-green-500' : 'text-gray-300 dark:text-white/20' }}">8+</span>
                                            <span class="{{ preg_match('/[A-Z]/', $password) ? 'text-green-500' : 'text-gray-300 dark:text-white/20' }}">A</span>
                                            <span class="{{ preg_match('/[0-9]/', $password) ? 'text-green-500' : 'text-gray-300 dark:text-white/20' }}">0</span>
                                            <span class="{{ preg_match('/[^A-Za-z0-9]/', $password) ? 'text-green-500' : 'text-gray-300 dark:text-white/20' }}">#</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label for="reg-password-confirm" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Confirm Password') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input wire:model="password_confirmation" type="{{ $showPassword ? 'text' : 'password' }}" id="reg-password-confirm" placeholder="{{ __('Re-enter password') }}" autocomplete="new-password"
                                           class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white text-sm placeholder-gray-400 dark:placeholder-white/25 focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                                </div>
                                @error('password_confirmation')
                                    <p class="flex items-start gap-1.5 text-xs text-red-500 mt-1.5">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Submit — site primary button --}}
                            <button type="submit" wire:loading.attr="disabled" wire:target="register"
                                    class="btn btn-primary btn-shine w-full !py-3.5 !rounded-xl uppercase tracking-widest font-black text-sm">
                                <span wire:loading.remove wire:target="register" class="inline-flex items-center gap-2">
                                    {{ __('Create Account') }}
                                    <svg class="icon-sm btn-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                </span>
                                <span wire:loading wire:target="register" class="inline-flex items-center gap-2">
                                    <svg class="icon-sm icon-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    {{ __('Creating account...') }}
                                </span>
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Security note --}}
                    <div class="flex items-center justify-center gap-2 mt-7 pt-5 border-t border-gray-100 dark:border-white/10">
                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-white/25 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
                        <p class="text-xs text-gray-400 dark:text-white/25">{{ __('bcrypt hashed · SSL encrypted') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scoped styles --}}
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
