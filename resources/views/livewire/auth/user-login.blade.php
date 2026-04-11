<div>
    {{-- Hero Banner --}}
    <div class="hero-gradient text-white py-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-black mb-2">My <span class="text-brand-yellow">Account</span></h1>
            <p class="text-gray-300">Sign in to your account or create a new one</p>
        </div>
    </div>

    <div class="min-h-[60vh] flex items-center justify-center py-12 px-4 bg-gray-50">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Tab Buttons --}}
                <div class="flex border-b border-gray-200">
                    <button
                        wire:click="switchTab(true)"
                        class="flex-1 py-4 text-center font-bold text-sm uppercase tracking-wider transition-all duration-300
                            {{ $isLoginTab ? 'text-brand-red border-b-2 border-brand-red bg-red-50/50' : 'text-gray-400 hover:text-gray-600' }}"
                        id="tab-sign-in"
                    >
                        🔐 Sign In
                    </button>
                    <button
                        wire:click="switchTab(false)"
                        class="flex-1 py-4 text-center font-bold text-sm uppercase tracking-wider transition-all duration-300
                            {{ !$isLoginTab ? 'text-brand-red border-b-2 border-brand-red bg-red-50/50' : 'text-gray-400 hover:text-gray-600' }}"
                        id="tab-register"
                    >
                        ✨ Register
                    </button>
                </div>

                <div class="p-8">

                    {{-- ============ SIGN IN FORM ============ --}}
                    @if($isLoginTab)
                    <form wire:submit="login">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg shadow-red-200">
                                <span class="text-brand-yellow font-black text-2xl">W</span>
                            </div>
                            <h2 class="text-2xl font-black text-brand-black">Welcome Back</h2>
                            <p class="text-gray-400 text-sm mt-1">Sign in to your Win Win account</p>
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="login-email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">✉️</span>
                                <input
                                    wire:model="loginEmail"
                                    type="email"
                                    id="login-email"
                                    placeholder="your@email.com"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-red focus:border-brand-red outline-none transition text-sm"
                                    autocomplete="email"
                                >
                            </div>
                            @error('loginEmail')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label for="login-password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
                                <input
                                    wire:model="loginPassword"
                                    type="{{ $showPassword ? 'text' : 'password' }}"
                                    id="login-password"
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-red focus:border-brand-red outline-none transition text-sm"
                                    autocomplete="current-password"
                                >
                                <button
                                    type="button"
                                    wire:click="$toggle('showPassword')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm"
                                >
                                    {{ $showPassword ? '🙈' : '👁️' }}
                                </button>
                            </div>
                            @error('loginPassword')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    wire:model="remember"
                                    type="checkbox"
                                    class="w-4 h-4 rounded border-gray-300 text-brand-red focus:ring-brand-red"
                                >
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            id="btn-login"
                            class="w-full bg-brand-red text-white py-3 rounded-xl font-bold text-sm uppercase tracking-wider hover:bg-red-700 transition-all duration-300 shadow-lg shadow-red-200 hover:shadow-red-300 flex items-center justify-center gap-2 disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="login">Sign In →</span>
                            <span wire:loading wire:target="login" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Signing in...
                            </span>
                        </button>
                    </form>

                    {{-- ============ REGISTER FORM ============ --}}
                    @else
                    <form wire:submit="register">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-brand-black rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg shadow-gray-300">
                                <span class="text-brand-yellow font-black text-2xl">✨</span>
                            </div>
                            <h2 class="text-2xl font-black text-brand-black">Create Account</h2>
                            <p class="text-gray-400 text-sm mt-1">Join the Win Win family today</p>
                        </div>

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="reg-name" class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">👤</span>
                                <input
                                    wire:model="name"
                                    type="text"
                                    id="reg-name"
                                    placeholder="Your full name"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-red focus:border-brand-red outline-none transition text-sm"
                                    autocomplete="name"
                                >
                            </div>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><span>⚠️</span> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="reg-email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">✉️</span>
                                <input
                                    wire:model="email"
                                    type="email"
                                    id="reg-email"
                                    placeholder="your@email.com"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-red focus:border-brand-red outline-none transition text-sm"
                                    autocomplete="email"
                                >
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><span>⚠️</span> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label for="reg-password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
                                <input
                                    wire:model.live="password"
                                    type="{{ $showPassword ? 'text' : 'password' }}"
                                    id="reg-password"
                                    placeholder="Min. 8 characters"
                                    class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-red focus:border-brand-red outline-none transition text-sm"
                                    autocomplete="new-password"
                                >
                                <button
                                    type="button"
                                    wire:click="$toggle('showPassword')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm"
                                >
                                    {{ $showPassword ? '🙈' : '👁️' }}
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><span>⚠️</span> {{ $message }}</p>
                            @enderror

                            {{-- Password Strength Indicator --}}
                            @if(strlen($password) > 0)
                            @php
                                $strength = 0;
                                if (strlen($password) >= 8) $strength++;
                                if (preg_match('/[A-Z]/', $password)) $strength++;
                                if (preg_match('/[a-z]/', $password)) $strength++;
                                if (preg_match('/[0-9]/', $password)) $strength++;
                                if (preg_match('/[^A-Za-z0-9]/', $password)) $strength++;

                                $strengthLabel = match(true) {
                                    $strength <= 1 => 'Very Weak',
                                    $strength === 2 => 'Weak',
                                    $strength === 3 => 'Medium',
                                    $strength === 4 => 'Strong',
                                    default => 'Very Strong',
                                };

                                $strengthColor = match(true) {
                                    $strength <= 1 => 'bg-red-500',
                                    $strength === 2 => 'bg-orange-500',
                                    $strength === 3 => 'bg-yellow-500',
                                    $strength === 4 => 'bg-green-500',
                                    default => 'bg-emerald-500',
                                };

                                $strengthWidth = match(true) {
                                    $strength <= 1 => 'w-1/5',
                                    $strength === 2 => 'w-2/5',
                                    $strength === 3 => 'w-3/5',
                                    $strength === 4 => 'w-4/5',
                                    default => 'w-full',
                                };
                            @endphp
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="{{ $strengthColor }} {{ $strengthWidth }} h-1.5 rounded-full transition-all duration-500"></div>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-xs text-gray-500">Strength: <span class="font-semibold {{ str_replace('bg-', 'text-', $strengthColor) }}">{{ $strengthLabel }}</span></p>
                                </div>
                                <div class="flex gap-1 flex-wrap mt-1">
                                    <span class="text-xs {{ strlen($password) >= 8 ? 'text-green-600' : 'text-gray-400' }}">✓ 8+ chars</span>
                                    <span class="text-xs {{ preg_match('/[A-Z]/', $password) ? 'text-green-600' : 'text-gray-400' }}">✓ Uppercase</span>
                                    <span class="text-xs {{ preg_match('/[a-z]/', $password) ? 'text-green-600' : 'text-gray-400' }}">✓ Lowercase</span>
                                    <span class="text-xs {{ preg_match('/[0-9]/', $password) ? 'text-green-600' : 'text-gray-400' }}">✓ Number</span>
                                    <span class="text-xs {{ preg_match('/[^A-Za-z0-9]/', $password) ? 'text-green-600' : 'text-gray-400' }}">✓ Symbol</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-6">
                            <label for="reg-password-confirm" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔒</span>
                                <input
                                    wire:model="password_confirmation"
                                    type="{{ $showPassword ? 'text' : 'password' }}"
                                    id="reg-password-confirm"
                                    placeholder="Re-enter password"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-red focus:border-brand-red outline-none transition text-sm"
                                    autocomplete="new-password"
                                >
                            </div>
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><span>⚠️</span> {{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <button
                            type="submit"
                            id="btn-register"
                            class="w-full bg-brand-black text-brand-yellow py-3 rounded-xl font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-all duration-300 shadow-lg shadow-gray-300 hover:shadow-gray-400 flex items-center justify-center gap-2 disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="register">Create Account ✨</span>
                            <span wire:loading wire:target="register" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Creating account...
                            </span>
                        </button>
                    </form>
                    @endif

                </div>

                {{-- Security Badge --}}
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-2 text-xs text-gray-400">
                        <span>🔐</span>
                        <span>Protected with <strong class="text-gray-500">bcrypt</strong> hashing • <strong class="text-gray-500">SSL</strong> encrypted</span>
                    </div>
                </div>

            </div>

            {{-- Admin Link --}}
            <div class="text-center mt-6">
                <a href="{{ route('admin.login') }}" class="text-xs text-gray-400 hover:text-brand-red transition">
                    Admin Portal →
                </a>
            </div>

        </div>
    </div>
</div>
