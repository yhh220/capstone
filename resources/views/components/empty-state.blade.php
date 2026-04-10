@props([
    'title' => 'Nothing found',
    'description' => 'We could not find what you were looking for.',
    'actionLabel' => null,
    'actionUrl' => '#',
])

<div class="flex flex-col items-center justify-center text-center p-8 sm:p-12 md:p-16 lg:p-20 w-full max-w-4xl mx-auto rounded-3xl bg-white dark:bg-gray-800/80 shadow-sm border border-gray-100 dark:border-gray-800 transition-all ease-in-out duration-300">
    
    <!-- Illustration Graphic (Responsive Sizing) -->
    <div class="relative mb-6 sm:mb-8 group">
        <!-- Colored background shape blast -> Creates the dynamic premium feel -->
        <div class="absolute inset-0 bg-brand-yellow/20 dark:bg-brand-yellow/10 rounded-full blur-2xl scale-125 transform group-hover:scale-150 transition-transform duration-500"></div>
        
        <!-- Center Icon Box (Tilted just like the reference design) -->
        <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 bg-gray-50 dark:bg-gray-900 rounded-3xl -rotate-6 shadow-inner flex items-center justify-center relative z-10 border border-gray-200 dark:border-gray-700 transition-transform duration-300 group-hover:-rotate-3">
            <!-- Checking if generic slot for icon is filled, otherwise fallback to toolbox -->
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <svg class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            @endif
        </div>

        <!-- Alert Notification Badge (The little exclamation mark from reference) -->
        <div class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 md:-bottom-4 md:-right-4 w-10 h-10 sm:w-12 sm:h-12 bg-brand-red text-white flex items-center justify-center rounded-full shadow-xl border-4 border-white dark:border-gray-800 z-20 animate-bounce" style="animation-duration: 2s;">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
    </div>

    <!-- Text Area (Responsive typography) -->
    <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-3 leading-tight tracking-tight px-4">
        {{ __($title) }}
    </h3>
    <p class="text-sm sm:text-base md:text-lg text-gray-500 dark:text-gray-400 mb-8 max-w-md md:max-w-xl mx-auto leading-relaxed px-4">
        {{ __($description) }}
    </p>

    <!-- Action Button (Mobile full-width, Desktop auto-width) -->
    @if($actionLabel)
        <div class="w-full sm:w-auto px-4 sm:px-0">
            <a href="{{ $actionUrl }}" class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3.5 sm:py-4 text-sm sm:text-base font-bold text-white transition-all bg-brand-red rounded-full hover:bg-red-700 active:scale-95 sm:hover:scale-105 shadow-md hover:shadow-lg hover:shadow-brand-red/20 focus:outline-none focus:ring-4 focus:ring-brand-red/30">
                {{ __($actionLabel) }}
            </a>
        </div>
    @endif
</div>
