<?php

namespace Database\Seeders;

use App\Models\ChatbotFaq;
use App\Services\Chat\MockDriver;
use Illuminate\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    /**
     * Seed the editable Chatbot FAQs table from the bot's built-in knowledge so
     * the admin can see and manage every answer in one place. Keyed on topic
     * with updateOrCreate, so re-running tops up new built-ins without
     * clobbering answers the shop has edited.
     */
    public function run(): void
    {
        // Admin-facing topic labels, in the same order as builtinKnowledge().
        $topics = [
            'Greeting',
            'Operating hours',
            'Location & directions',
            'Booking an appointment',
            'Pricing',
            'Warranty',
            'Air-conditioning (aircond)',
            'Pickup / takeaway',
            'Car audio systems',
            'Window tinting',
            'Dashcams',
            'Wrap / PPF / coating',
            'Accessories (general)',
            'Installation',
            'Payment methods',
            'Delivery / shipping',
            'Contact details',
            'Privacy & data',
            'Terms of service',
            'Vehicle fitment',
            'Installation duration',
            'Custom installation',
            'Cancel / reschedule booking',
            'Online shopping',
            'Languages supported',
            'Promotions & discounts',
            'Returns & refunds',
            'Instalment / financing',
            'Home / onsite service',
            'Trade-in / second-hand',
            'Social media',
            'Deposit / downpayment',
            'Thank you',
        ];

        $rules = (new MockDriver())->builtinKnowledge();

        foreach ($rules as $i => $rule) {
            $topic = $topics[$i] ?? ('Topic ' . ($i + 1));

            ChatbotFaq::updateOrCreate(
                ['topic' => $topic],
                [
                    'keywords'  => $rule['keywords'] ?? [],
                    'priority'  => $rule['priority'] ?? 50,
                    'reply_en'  => $rule['reply']['en'] ?? '',
                    'reply_ms'  => $rule['reply']['ms'] ?? null,
                    'reply_zh'  => $rule['reply']['zh'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('Seeded ' . count($rules) . ' chatbot FAQs.');
    }
}
