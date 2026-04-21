@if (filament()->hasDarkMode() && ! filament()->hasDarkModeForced())
    <div
        x-data="{
            theme: localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value),
            toggle() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark'
                localStorage.setItem('theme', this.theme)
                this.$dispatch('theme-changed', this.theme)
            }
        }"
        style="position: fixed; top: 1rem; right: 1rem; z-index: 50;"
    >
        <button
            type="button"
            @click="toggle"
            :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 dark:bg-gray-800/60 dark:hover:bg-gray-700/80 border border-white/20 dark:border-gray-700 text-gray-600 dark:text-gray-300 backdrop-blur-sm transition-colors duration-200"
        >
            {{-- Sun icon (shown in dark mode → click to go light) --}}
            <svg x-show="theme === 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
            {{-- Moon icon (shown in light mode → click to go dark) --}}
            <svg x-show="theme !== 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
        </button>
    </div>
@endif
