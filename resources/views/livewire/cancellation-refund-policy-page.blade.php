<div>
    @php
        $storeEmail = config('services.store.email', 'winwincaraudio@gmail.com');
        $storePhoneDisplay = config('services.store.phone_display', '+60 12-345 6789');
    @endphp

    <div class="bg-gray-100 dark:bg-gray-900 text-brand-black dark:text-white py-12">
        <div class="max-w-5xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black mb-2">{{ __('Cancellation & Refund Policy') }}</h1>
            <p class="text-gray-400">{{ __('Last Updated: June 2026') }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 sm:p-8 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed">

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('Clear rules, before you pay.') }}</h2>
                <p>{{ __('We want you to know exactly what happens if you change your mind about an order — before you ever pay for it. This page explains, in plain English, when a cancelled order is refunded, how much, and when cancellation is no longer possible. The numbers below are always live and match what the system actually enforces.') }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('1. What Counts as Paid') }}</h2>
                <p>{{ __("An order is considered paid once our checkout confirms your payment and your order status moves to Processing. Orders that haven't been paid yet can be cancelled any time at no cost — there's nothing to refund since nothing was charged.") }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('2. Full Refund Window') }}</h2>
                <p>{{ __("If you cancel a paid order within :hours hours of payment, you get a 100% refund — no questions asked, as long as the order hasn't shipped yet.", ['hours' => $fullRefundHours]) }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('3. Cancellation After the Full Refund Window') }}</h2>
                <p>{{ __("If you cancel after the :hours-hour window has passed, but your order still hasn't shipped, we deduct a :fee% processing fee from the refund — the rest is recorded for our team to send back to you.", ['hours' => $fullRefundHours, 'fee' => $feePercent]) }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('4. Once an Order Has Shipped') }}</h2>
                <p>{{ __("Once your order has shipped or been delivered, it can no longer be cancelled through this self-service flow. If there's a problem with a delivered item — faulty, wrong item, etc. — please reach out to us directly and our team will help you sort it out through our returns/exchange process.") }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('5. How Refunds Are Sent') }}</h2>
                <p>{{ __("When you cancel a paid order, the refund amount is recorded immediately and you'll get an email confirming it. Refunds are returned to your original payment method by our team, typically within 3 to 7 working days — you'll receive a second, separate email once the refund has been sent.") }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('6. How to Cancel') }}</h2>
                <p class="mb-2">{{ __('Go to My Account, find the order, and use the Cancel Order button — it will show you exactly how much refund applies before you confirm. You can also call or WhatsApp us and our team can cancel it for you under the exact same rules.') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>{{ __('WhatsApp:') }}</strong> {{ $storePhoneDisplay }}</li>
                    <li><strong>{{ __('Email:') }}</strong> <a href="mailto:{{ $storeEmail }}" class="text-brand-red hover:underline">{{ $storeEmail }}</a></li>
                </ul>
            </section>

            <section>
                <hr class="my-8 border-gray-100 dark:border-gray-700">
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-3">{{ __('7. Keeping This Policy Clear') }}</h2>
                <p>{{ __('We may adjust the refund window or fee shown above from time to time. Whenever we do, this page always reflects the current numbers — what you see here is exactly what the system will apply if you cancel right now.') }}</p>
            </section>

        </div>
    </div>
</div>
