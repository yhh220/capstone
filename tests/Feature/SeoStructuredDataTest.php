<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The AutoPartsStore JSON-LD must carry real local-business details (address,
 * phone, map coordinates, opening hours) so Google can surface the shop in
 * local search and Maps — not just a bare business name. Values come from the
 * store config; opening hours are derived from the booking business-hours
 * settings so structured hours can't drift from what the site offers.
 */
class SeoStructuredDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_jsonld_includes_full_business_details(): void
    {
        Setting::setValue('BUSINESS_HOURS_START', '09:00');
        Setting::setValue('BUSINESS_HOURS_END', '18:00');
        Setting::setValue('BUSINESS_CLOSED_WEEKDAYS', '5'); // closed Friday

        $html = $this->get('/')->assertOk()->getContent();

        // Structured data block is present and typed as a local business.
        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertStringContainsString('AutoPartsStore', $html);

        // Address, phone, and geo made it in.
        $this->assertStringContainsString('PostalAddress', $html);
        $this->assertStringContainsString(config('services.store.city'), $html);
        $this->assertStringContainsString(config('services.store.phone_display'), $html);
        $this->assertStringContainsString('GeoCoordinates', $html);

        // Opening hours derived from settings: open days exclude Friday.
        $this->assertStringContainsString('OpeningHoursSpecification', $html);
        $this->assertStringContainsString('Monday', $html);
        $this->assertStringNotContainsString('"Friday"', $html);
    }

    public function test_robots_txt_declares_the_sitemap_and_blocks_admin(): void
    {
        // robots.txt is a static public/ file (served by the web server, not a
        // Laravel route), so assert against its contents directly.
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Sitemap:', $robots);
        $this->assertStringContainsString('sitemap.xml', $robots);
    }

    public function test_sitemap_uses_the_production_url_not_the_local_domain(): void
    {
        // The canonical site URL must be an https production host, never the
        // local .test domain — Google rejects a sitemap whose URLs don't match
        // the domain it is served from ("URL not allowed").
        $url = config('services.store.url');
        $this->assertStringStartsWith('https://', $url);
        $this->assertStringNotContainsString('.test', $url);

        // The committed sitemap file must already be on that production host.
        $sitemap = file_get_contents(public_path('sitemap.xml'));
        $this->assertStringNotContainsString('.test', $sitemap);
        $this->assertStringContainsString($url, $sitemap);
    }
}
