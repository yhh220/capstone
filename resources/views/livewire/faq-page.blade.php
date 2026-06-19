<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Frequently Asked Questions') }}</h1>
            <p class="text-gray-500 dark:text-gray-400">{{ __('Common questions about products, bookings, showroom visits, and online shopping.') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-10 sm:py-12" x-data="{ active: null }">
        @forelse($faqGroups as $category => $faqs)
            @if($category !== '')
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-brand-red mt-8 mb-3 first:mt-0">{{ $category }}</h2>
            @endif

            <div class="space-y-3 sm:space-y-4">
                @foreach($faqs as $faq)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <button
                        @click="active === {{ $faq->id }} ? active = null : active = {{ $faq->id }}"
                        :aria-expanded="active === {{ $faq->id }}"
                        class="w-full flex items-center justify-between gap-4 p-5 sm:p-6 text-left focus:outline-none transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                        <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white uppercase">{{ $faq->question() }}</h3>
                        <span class="text-gray-400 transform transition-transform duration-300 flex-shrink-0"
                              :class="active === {{ $faq->id }} ? 'rotate-45' : ''">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </span>
                    </button>
                    <div x-show="active === {{ $faq->id }}" x-collapse style="display: none;">
                        <div class="px-5 sm:px-6 pb-5 sm:pb-6 pt-0 text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ $faq->answer() }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-12">{{ __('No questions have been added yet.') }}</p>
        @endforelse
    </div>
</div>
