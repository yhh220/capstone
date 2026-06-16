<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$faqs = \App\Models\ChatbotFaq::all(['topic', 'keywords', 'priority', 'reply_en', 'reply_ms', 'reply_zh', 'is_active']);
file_put_contents(__DIR__ . '/database/seeders/chatbot_faqs.json', $faqs->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Exported FAQs to database/seeders/chatbot_faqs.json\n";
