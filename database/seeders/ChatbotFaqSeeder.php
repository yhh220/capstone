<?php

namespace Database\Seeders;

use App\Models\ChatbotFaq;
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
        $jsonPath = __DIR__.'/chatbot_faqs.json';

        if (! file_exists($jsonPath)) {
            $this->command?->error("FAQ JSON file not found at {$jsonPath}");

            return;
        }

        $rules = json_decode(file_get_contents($jsonPath), true);

        if (! $rules) {
            $this->command?->error('Failed to decode FAQ JSON.');

            return;
        }

        foreach ($rules as $rule) {
            ChatbotFaq::updateOrCreate(
                ['topic' => $rule['topic']],
                [
                    'keywords' => $rule['keywords'] ?? [],
                    'priority' => $rule['priority'] ?? 50,
                    'reply_en' => $rule['reply_en'] ?? '',
                    'reply_ms' => $rule['reply_ms'] ?? null,
                    'reply_zh' => $rule['reply_zh'] ?? null,
                    'is_active' => $rule['is_active'] ?? true,
                ]
            );
        }

        $this->command?->info('Seeded '.count($rules).' chatbot FAQs from JSON.');
    }
}
