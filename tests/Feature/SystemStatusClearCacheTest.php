<?php

namespace Tests\Feature;

use App\Filament\Pages\SystemStatus;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The admin "Clear System Cache" button must drop only the app's own content
 * caches — never the security state that also lives in the database cache
 * store. It used to call Cache::flush(), which wiped login lockouts, IP
 * blocks, live OTP codes, and every rate-limit counter along with the content.
 */
class SystemStatusClearCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_clears_content_caches_but_preserves_security_state(): void
    {
        Setting::create(['key' => 'BUSINESS_HOURS_START', 'value' => '09:00']);

        // Content caches — must be gone afterwards.
        Cache::put('setting_BUSINESS_HOURS_START', '09:00', 3600);
        Cache::put('dashboard_stats', ['x' => 1], 60);
        Cache::put('chatbot_faqs', ['faq'], 3600);
        Cache::put('chatbot_services', ['svc'], 600);

        // Security state — must survive.
        Cache::put('login_block:ip:1.2.3.4', true, 3600);
        Cache::put('login_fails:email:victim@example.test', 4, 7200);
        Cache::put('otp:register:new@example.test', 'hash', 600);
        Cache::put('otp_attempts:register:new@example.test', 2, 600);
        Cache::put('booking-submit:5.6.7.8', 3, 600); // a RateLimiter-style key

        $admin = User::factory()->create(['role' => 'admin']);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($admin, 'admin');

        Livewire::test(SystemStatus::class)->call('clearCache');

        // Content gone.
        $this->assertFalse(Cache::has('setting_BUSINESS_HOURS_START'));
        $this->assertFalse(Cache::has('dashboard_stats'));
        $this->assertFalse(Cache::has('chatbot_faqs'));
        $this->assertFalse(Cache::has('chatbot_services'));

        // Security state untouched — the whole point of the fix.
        $this->assertTrue(Cache::has('login_block:ip:1.2.3.4'), 'An active IP block must survive a content-cache clear.');
        $this->assertSame(4, Cache::get('login_fails:email:victim@example.test'));
        $this->assertTrue(Cache::has('otp:register:new@example.test'), 'A live OTP code must not be invalidated by clearing content caches.');
        $this->assertSame(2, Cache::get('otp_attempts:register:new@example.test'));
        $this->assertSame(3, Cache::get('booking-submit:5.6.7.8'), 'Rate-limit counters must not reset.');
    }
}
