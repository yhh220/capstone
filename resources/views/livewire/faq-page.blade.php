<div>
    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Frequently Asked Questions') }}</h1>
            <p class="text-gray-400">{{ __('Common questions about products, bookings, showroom visits, and online shopping.') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-12" x-data="{ active: null }">
        <div class="space-y-4">
            @foreach([
                ['Do you sell online?', 'Online shopping may be turned on or off depending on current operations. If prices are hidden, please WhatsApp the store for assistance.'],
                ['Can I book a service online?', 'Yes. You can choose a service, pick an available date and time, and submit your vehicle details from the booking page.'],
                ['How do I know if a product fits my car?', 'Use the compatibility guidance on the site, then contact the showroom on WhatsApp for a final confirmation before visiting or purchasing.'],
                ['Do you accept real online payments?', 'Online payments are currently processed through store confirmation. Our staff will guide you through the purchasing process.'],
                ['Can I cancel my booking?', 'Yes. Use your booking manage link or token to review and cancel a booking before your appointment time.'],
                ['Do you support multiple languages?', 'The site supports English, Bahasa Malaysia, and Chinese for customer browsing.'],
                ['Where is your showroom located?', 'Our showroom is located in Shah Alam, Selangor. Visit us during operating hours for a hands-on experience with our products and services. Contact us on WhatsApp for the exact address and directions.'],
                ['How long does installation take?', 'Installation time varies depending on the product and complexity. A basic audio setup may take 1–2 hours, while full custom installations may require a full day. Our team will give you an estimated time when you book.'],
                ['Do you provide warranty for products?', 'Yes. Warranty coverage depends on the brand and product. Our staff will inform you of the warranty terms at the time of purchase. For warranty claims, please bring your receipt and visit the showroom.'],
                ['Can I request a custom installation?', 'Absolutely. We offer custom audio and accessories installation tailored to your vehicle. Contact us via WhatsApp or visit the showroom to discuss your requirements with our team.'],
            ] as $index => [$question, $answer])
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <button 
                    @click="active === {{ $index }} ? active = null : active = {{ $index }}"
                    class="w-full flex items-center justify-between p-6 text-left focus:outline-none transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
                >
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase pr-4">{{ __($question) }}</h2>
                    <span class="text-gray-400 transform transition-transform duration-300 flex-shrink-0"
                          :class="active === {{ $index }} ? 'rotate-45' : ''">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                </button>
                <div 
                    x-show="active === {{ $index }}"
                    x-collapse
                    style="display: none;"
                >
                    <div class="px-6 pb-6 pt-0 text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __($answer) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
