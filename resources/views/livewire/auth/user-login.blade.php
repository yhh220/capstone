<div class="min-h-screen flex bg-white dark:bg-[#0C0C0E]">

    {{-- Left: Brand panel (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-[44%] xl:w-[48%] flex-col justify-between p-12 text-white relative overflow-hidden" style="background:#0C0C0E;">
        <div class="absolute inset-0" style="background:linear-gradient(135deg,#0C0C0E 60%,#1a0a09);" aria-hidden="true"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px" style="background:rgba(200,65,61,0.3);" aria-hidden="true"></div>

        {{-- Logo --}}
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo/logo-light.svg') }}" alt="Win Win Car Audio" class="h-8 w-auto">
            </a>
        </div>

        {{-- Centre: Big text --}}
        <div class="relative z-10">
            <p class="font-mono text-[10px] tracking-[0.3em] uppercase mb-6" style="color:rgba(200,65,61,0.8);">{{ __('Shah Alam, Selangor') }}</p>
            <h2 class="font-display font-black text-[clamp(2.4rem,3.5vw,3.4rem)] leading-[0.93] uppercase text-white mb-8">
                {{ __("Shah Alam's") }}<br>
                <span style="color:#C8413D;">{{ __('Car Audio') }}</span><br>
                {{ __('Specialist.') }}
            </h2>
            <div class="space-y-3">
                @foreach([__('Curated brands & products'), __('Expert installation'), __('Walk-in showroom welcome')] as $point)
                <div class="flex items-center gap-3 text-sm text-white/50">
                    <span class="text-xs" style="color:#C8413D;">■</span>
                    {{ $point }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom: Stats --}}
        <div class="relative z-10 flex gap-8">
            <div>
                <div class="font-display font-black text-2xl text-white">1000+</div>
                <div class="font-mono text-[10px] tracking-widest uppercase text-white/30">{{ __('Installations') }}</div>
            </div>
            <div class="w-px bg-white/10 self-stretch"></div>
            <div>
                <div class="font-display font-black text-2xl text-white">20+</div>
                <div class="font-mono text-[10px] tracking-widest uppercase text-white/30">{{ __('Brands') }}</div>
            </div>
        </div>
    </div>

    {{-- Right: Form panel --}}
    <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-10 lg:px-16 xl:px-20">

        {{-- Mobile logo --}}
        <div class="lg:hidden mb-10">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="Win Win Car Audio" class="h-7 w-auto dark:hidden">
                <img src="{{ asset('images/logo/logo-light.svg') }}" alt="Win Win Car Audio" class="h-7 w-auto hidden dark:block">
            </a>
        </div>

        <div class="w-full max-w-sm mx-auto lg:mx-0">

            {{-- Tab Switcher --}}
            <div class="flex border-b border-gray-200 dark:border-white/10 mb-8">
                <button
                    wire:click="switchTab(true)"
                    class="flex-1 pb-3 text-sm font-bold uppercase tracking-widest transition-all duration-200 {{ $isLoginTab ? 'border-b-2 text-gray-900 dark:text-white' : 'text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60' }}"
                    style="{{ $isLoginTab ? 'border-color:#C8413D;' : '' }}"
                >
                    {{ __('Sign In') }}
                </button>
                <button
                    wire:click="switchTab(false)"
                    class="flex-1 pb-3 text-sm font-bold uppercase tracking-widest transition-all duration-200 {{ !$isLoginTab ? 'border-b-2 text-gray-900 dark:text-white' : 'text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60' }}"
                    style="{{ !$isLoginTab ? 'border-color:#C8413D;' : '' }}"
                >
                    {{ __('Register') }}
                </button>
            </div>

            {{-- ============ SIGN IN FORM ============ --}}
            @if($isLoginTab)
            <div>
                <h1 class="font-display font-black text-3xl uppercase text-gray-900 dark:text-white mb-1">{{ __('Welcome Back') }}</h1>
                <p class="text-sm text-gray-400 dark:text-white/35 mb-8">{{ __('Sign in to your account') }}</p>

                <form wire:submit="login" class="space-y-5">

                    {{-- Email --}}
                    <div>
                        <label for="login-email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Email') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </span>
                            <input
                                wire:model="loginEmail"
                                type="email"
                                id="login-email"
                                placeholder="your@email.com"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-[#C8413D] transition-colors duration-200"
                                autocomplete="email"
                            >
                        </div>
                        @error('loginEmail')
                            <p class="flex items-center gap-1.5 text-xs text-red-500 mt-1.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="login-password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Password') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input
                                wire:model="loginPassword"
                                type="{{ $showPassword ? 'text' : 'password' }}"
                                id="login-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-12 py-3 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-[#C8413D] transition-colors duration-200"
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                wire:click="$toggle('showPassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60 transition-colors"
                                aria-label="{{ $showPassword ? __('Hide password') : __('Show password') }}"
                            >
                                @if($showPassword)
                                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                @endif
                            </button>
                        </div>
                        @error('loginPassword')
                            <p class="flex items-center gap-1.5 text-xs text-red-500 mt-1.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2">
                        <input
                            wire:model="remember"
                            type="checkbox"
                            id="remember"
                            class="w-4 h-4 border-gray-300 dark:border-white/20 accent-[#C8413D]"
                        >
                        <label for="remember" class="text-sm text-gray-500 dark:text-white/40 cursor-pointer">{{ __('Remember me') }}</label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="group relative overflow-hidden w-full flex items-center justify-center gap-3 py-3.5 text-sm font-black uppercase tracking-widest text-white btn-shine transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                        style="background:#C8413D;"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="login" class="flex items-center gap-3">
                            {{ __('Sign In') }}
                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1"></i>
                        </span>
                        <span wire:loading wire:target="login" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ __('Signing in...') }}
                        </span>
                    </button>
                </form>
            </div>

            {{-- ============ REGISTER FORM ============ --}}
            @else
            <div>
                <h1 class="font-display font-black text-3xl uppercase text-gray-900 dark:text-white mb-1">{{ __('Create Account') }}</h1>
                <p class="text-sm text-gray-400 dark:text-white/35 mb-8">{{ __('Join the Win Win community') }}</p>

                <form wire:submit="register" class="space-y-5">
                    <x-honeypot livewire-model="honeypotData" />

                    {{-- Full Name --}}
                    <div>
                        <label for="reg-name" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Full Name') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </span>
                            <input
                                wire:model="name"
                                type="text"
                                id="reg-name"
                                placeholder="{{ __('Your full name') }}"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-[#C8413D] transition-colors duration-200"
                                autocomplete="name"
                            >
                        </div>
                        @error('name')
                            <p class="flex items-center gap-1.5 text-xs text-red-500 mt-1.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="reg-email" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Email') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </span>
                            <input
                                wire:model="email"
                                type="email"
                                id="reg-email"
                                placeholder="your@email.com"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-[#C8413D] transition-colors duration-200"
                                autocomplete="email"
                            >
                        </div>
                        @error('email')
                            <p class="flex items-center gap-1.5 text-xs text-red-500 mt-1.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="reg-password" class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-2">{{ __('Password') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input
                                wire:model.live="password"
                                type="{{ $showPassword ? 'text' : 'password' }}"
                                id="reg-password"
                                placeholder="{{ __('Min. 8 characters') }}"
                                class="w-full pl-10 pr-12 py-3 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-[#C8413D] transition-colors duration-200"
                                autocomplete="new-password"
                            >
                            <button
                                type="button"
                                wire:click="$toggle('showPassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60 transition-colors"
                                aria-label="{{ $showPassword ? __('Hide password') : __('Show password') }}"
                            >
                                @if($showPassword)
                                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                @endif
                            </button>
                        </div>
                        @error('password')
                            <p class="flex items-center gap-1.5 text-xs text-red-500 mt-1.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $message }}
                            </p>
                        @enderror

                        {{-- Password strength: 5 square indicator blocks --}}
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
                        <div class="mt-2">
                            <div class="flex gap-1 mb-1">
                                @for($s = 1; $s <= 5; $s++)
                                <div class="flex-1 h-1 transition-colors duration-300 {{ $s <= $strength ? $activeColor : 'bg-gray-200 dark:bg-white/10' }}"></div>
                                @endfor
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-gray-400 dark:text-white/30">{{ $strengthLabels[max(0, $strength - 1)] }}</p>
                                <div class="flex gap-2 text-xs">
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
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 pointer-events-none">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </span>
                            <input
                                wire:model="password_confirmation"
                                type="{{ $showPassword ? 'text' : 'password' }}"
                                id="reg-password-confirm"
                                placeholder="{{ __('Re-enter password') }}"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white text-sm focus:outline-none focus:border-[#C8413D] transition-colors duration-200"
                                autocomplete="new-password"
                            >
                        </div>
                        @error('password_confirmation')
                            <p class="flex items-center gap-1.5 text-xs text-red-500 mt-1.5">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="group relative overflow-hidden w-full flex items-center justify-center gap-3 py-3.5 text-sm font-black uppercase tracking-widest text-white btn-shine transition-opacity duration-200 hover:opacity-90 disabled:opacity-50"
                        style="background:#0C0C0E;"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="register" class="flex items-center gap-3">
                            {{ __('Create Account') }}
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                        </span>
                        <span wire:loading wire:target="register" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ __('Creating account...') }}
                        </span>
                    </button>
                </form>
            </div>
            @endif

            {{-- Security note --}}
            <div class="flex items-center gap-2 mt-8 pt-6 border-t border-gray-100 dark:border-white/10">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-gray-300 dark:text-white/20 shrink-0"></i>
                <p class="text-xs text-gray-400 dark:text-white/25">{{ __('bcrypt hashed · SSL encrypted') }}</p>
            </div>
        </div>
    </div>
</div>
