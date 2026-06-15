<div>
    @php
        $storeEmail = config('services.store.email', 'winwincaraudio@gmail.com');
        $storePhoneDisplay = config('services.store.phone_display', '+60 12-345 6789');
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Privacy Policy') }}</h1>
            <p class="text-gray-400">{{ __('Last Updated: June 2026') }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed">
            
            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('Privacy is important. Yours is no exception.') }}</h2>
                <p>{{ __('Welcome to Win Win Car Audio! We’re a car audio and accessories shop based in Shah Alam, Malaysia. We believe that great service starts with trust. This page explains in plain English what information we collect when you use our website, why we need it, and how we keep it safe.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('1. What Information We Collect') }}</h2>
                <p class="mb-2">{{ __('We only ask for the information we actually need to help you. When you book an appointment, submit an enquiry, or message us, we may ask for your:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('Name, phone number, and email address') }}</li>
                    <li>{{ __('Vehicle details (so we know what fits your car)') }}</li>
                    <li>{{ __('Your booking preferences and enquiry messages') }}</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('2. How We Use Your Information') }}</h2>
                <p class="mb-2">{{ __('We use your details simply to provide the services you requested. Specifically, we use it to:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('Respond to your questions and messages') }}</li>
                    <li>{{ __('Manage and confirm your showroom appointments') }}</li>
                    <li>{{ __('Keep track of your orders and ensure smooth installations') }}</li>
                    <li>{{ __('Make our website easier to use for everyone') }}</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('3. Cookies & Tracking') }}</h2>
                <p>{{ __('We like to keep things simple. Our website uses basic session cookies just to make sure the site works properly while you’re browsing. We don’t use creepy tracking cookies to follow you around the internet with ads.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('4. How Long We Keep Your Data') }}</h2>
                <p>{{ __('We hold onto your information only for as long as it takes to help you, manage our daily operations, and handle any follow-up service your car might need. Once we no longer need it, it’s safely removed.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('5. Sharing Your Information') }}</h2>
                <p class="mb-2">{{ __('Here is our promise: We do not and will never sell your personal data. We only share the absolute minimum information necessary with trusted tools that help us run our shop, such as:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('WhatsApp, when you choose to chat with us directly') }}</li>
                    <li>{{ __('Secure email tools to send your booking confirmations') }}</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('6. Keeping Your Data Safe') }}</h2>
                <p>{{ __('We treat your information with respect. We’ve put in place reasonable and honest measures to make sure your data is protected against unauthorized access, loss, or misuse while you interact with our website.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('7. Your Rights') }}</h2>
                <p>{{ __('It’s your data, and you’re in control. If you ever want to see what information we have about you, correct a mistake, or ask us to delete your details from our records, just reach out to us. We’ll be happy to help.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('8. Updates to This Policy') }}</h2>
                <p>{{ __('As our shop and website grow, we might need to update this policy from time to time. Whenever we make changes, we’ll update this page so you always know what’s going on.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('9. Contact Us') }}</h2>
                <p class="mb-2">{{ __('If you ever have any questions about your privacy or how we handle your data, we’re just a message away. Please contact us at:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>{{ __('Email:') }}</strong> <a href="mailto:{{ $storeEmail }}" class="text-brand-red hover:underline">{{ $storeEmail }}</a></li>
                    <li><strong>{{ __('WhatsApp:') }}</strong> {{ $storePhoneDisplay }}</li>
                </ul>
            </section>

            <section>
                <hr class="my-8 border-gray-100 dark:border-gray-700">
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('10. Legal Compliance') }}</h2>
                <p class="mb-2">{{ __('This Privacy Policy is governed by and compliant with the following Malaysian laws:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('Personal Data Protection Act (PDPA 2010)') }}</li>
                    <li>{{ __('Computer Crimes Act 1997') }}</li>
                    <li>{{ __('Communications and Multimedia Act 1998') }}</li>
                    <li>{{ __('Copyright Act 1987') }}</li>
                </ul>
            </section>

        </div>
    </div>
</div>
