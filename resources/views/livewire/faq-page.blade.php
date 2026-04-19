<div>
    <div class="bg-brand-black text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Frequently Asked Questions') }}</h1>
            <p class="text-gray-400">{{ __('Common questions about products, bookings, showroom visits, and online shopping.') }}</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-12 space-y-4">
        @foreach([
            ['Do you sell online?', 'Online shopping may be turned on or off depending on current operations. If prices are hidden, please WhatsApp the store for assistance.'],
            ['Can I book a service online?', 'Yes. You can choose a service, pick an available date and time, and submit your vehicle details from the booking page.'],
            ['How do I know if a product fits my car?', 'Use the compatibility guidance on the site, then contact the showroom on WhatsApp for a final confirmation before visiting or purchasing.'],
            ['Do you accept real online payments?', 'Orders currently use a mock checkout flow for capstone purposes. Staff will still guide customers for real-world purchases.'],
            ['Can I cancel my booking?', 'Yes. Use your booking manage link or token to review and cancel a booking before your appointment time.'],
            ['Do you support multiple languages?', 'The site supports English, Bahasa Malaysia, and Chinese for customer browsing.'],
        ] as [$question, $answer])
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-2">{{ __($question) }}</h2>
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ __($answer) }}</p>
        </div>
        @endforeach
    </div>
</div>
