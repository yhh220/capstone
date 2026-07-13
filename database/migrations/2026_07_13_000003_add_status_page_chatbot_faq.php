<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('chatbot_faqs')->updateOrInsert(
            ['topic' => 'System Status'],
            [
                'keywords' => json_encode(['system status', 'website status', 'service status', 'site status', 'website down', 'outage', 'downtime', 'status sistem', 'status laman web', 'gangguan sistem', '系统状态', '网站状态', '服务状态', '网站打不开', '故障', '宕机'], JSON_UNESCAPED_UNICODE),
                'priority' => 95,
                'reply_en' => 'You can check the latest website, ordering, booking, email and chat-support updates on our public System Status page. Tap the button below to open it in a new tab.',
                'reply_ms' => 'Anda boleh menyemak kemas kini terkini tentang laman web, pesanan, tempahan, e-mel dan sokongan chat pada halaman Status Sistem awam kami. Ketik butang di bawah untuk membukanya dalam tab baharu.',
                'reply_zh' => '您可以在我们的公开系统状态页面查看网站、订单、预约、电子邮件与聊天支持的最新状态。点击下方按钮即可在新标签页打开。',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
        Cache::forget('chatbot_faqs');
    }

    public function down(): void
    {
        // Keep this owner-editable FAQ if the migration is rolled back later.
    }
};
