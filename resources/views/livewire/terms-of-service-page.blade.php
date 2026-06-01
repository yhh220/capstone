<div>
    @php
        $storeEmail = config('services.store.email', 'winwincaraudio@gmail.com');
        $storePhoneDisplay = config('services.store.phone_display', '+60 12-345 6789');
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Terms of Service') }}</h1>
            <p class="text-gray-400">{{ __('Last Updated: April 2026') }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed">
            
            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('Agreement to Terms') }}</h2>
                <p>{{ __('Welcome to Win Win Car Audio. These terms outline the rules for using our website. By browsing our products, making a booking, or getting in touch with us, you agree to these simple terms. If you do not agree, please do not use our site.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('1. Product Information') }}</h2>
                <p>{{ __('We try our best to make sure all product descriptions, prices, and compatibility notes on our website are accurate and up to date. However, things change. We reserve the right to correct any errors and update information at any time without prior notice. Always confirm final fitment and stock with us directly.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('2. Bookings & Appointments') }}</h2>
                <p>{{ __('When you request a booking online, it helps us know you’re coming, but it doesn’t guarantee an immediate slot until we confirm it with you. We ask that you provide accurate vehicle details and arrive on time so we can give your car the best care possible.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('3. Orders') }}</h2>
                <p>{{ __('Any orders placed through our website are subject to final confirmation by our team. Availability can fluctuate, so we will always verify stock and finalize details with you before proceeding with fulfillment.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('4. Website Usage') }}</h2>
                <p>{{ __('We built this website to help you find great audio solutions for your car. Please use it responsibly. Do not attempt to disrupt the site, access areas you shouldn’t, or submit false information through our forms and chat.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('5. Intellectual Property') }}</h2>
                <p>{{ __('All content on this website, including text, images, logos, and design, belongs to us. Please do not copy, reproduce, or steal our work.') }}</p>
                <p class="mt-2 font-semibold">&copy; {{ __('Win Win Car Audio 2026') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('6. Disclaimer') }}</h2>
                <p>{{ __('Our website and services are provided "as is". While we strive for perfection, we cannot promise that the website will always be completely error-free or uninterrupted.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('7. Limitation of Liability') }}</h2>
                <p>{{ __('To the maximum extent permitted by law, Win Win Car Audio is not responsible for any indirect or accidental damages that might occur from your use of this website or reliance on the information provided here.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('8. Changes to Terms') }}</h2>
                <p>{{ __('We may update these terms occasionally to reflect changes in our business. When we do, we will update the "Last Updated" date at the top of this page. Continuing to use the site means you accept those changes.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('9. Contact Us') }}</h2>
                <p class="mb-2">{{ __('If you have any questions about these terms or need urgent assistance, we’re always here to help. Reach out to us at:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>{{ __('Email:') }}</strong> <a href="mailto:{{ $storeEmail }}" class="text-brand-red hover:underline">{{ $storeEmail }}</a></li>
                    <li><strong>{{ __('WhatsApp:') }}</strong> {{ $storePhoneDisplay }}</li>
                </ul>
            </section>

        </div>
    </div>
</div>
