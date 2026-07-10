<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Leaflet (the map library) is only used on the Contact page, so it is loaded
 * there via @push rather than globally in the layout. This keeps ~46 KB of
 * render-blocking CSS + script off every other page while leaving the Contact
 * map fully functional.
 */
class AssetLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_embeds_the_google_map_by_coordinates(): void
    {
        $html = $this->get('/contact')->assertOk()->getContent();

        $this->assertStringContainsString('https://www.google.com/maps/embed', $html, 'Contact needs the Google Maps embed iframe.');
        // Pinned by coordinates, never by a text search: a name/address query
        // re-geocodes per viewer and once resolved to a different business a
        // few doors down (the documented place_cid incident).
        $this->assertStringContainsString(config('services.store.lat').','.config('services.store.lng'), $html);
        $this->assertStringContainsString('loading="lazy"', $html, 'The map iframe must not block first paint.');
        $this->assertStringNotContainsString('leaflet', $html, 'Leaflet was replaced by the Google Maps embed.');
    }

    public function test_home_page_does_not_load_leaflet(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('leaflet', $html, 'Pages without a map must not load Leaflet.');
    }

    /**
     * model-viewer must come from our own origin: the unversioned unpkg URL
     * went through a cold third-party CDN redirect on every visit, leaving the
     * homepage 3D card stuck on its spinner (and unpkg in the CSP allowlist).
     */
    public function test_home_page_serves_model_viewer_from_our_own_origin(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('vendor/model-viewer/model-viewer', $html, 'The homepage 3D card needs the self-hosted model-viewer bundle.');
        $this->assertStringNotContainsString('unpkg.com', $html, 'No third-party CDN scripts on the storefront.');
    }

    /**
     * The page loader lives in an @persist block, so its DOM is never re-rendered
     * across Livewire.navigate() soft-navigations (which is how the language
     * switch works). If its caption were a single server-rendered __() string it
     * would freeze on whatever locale rendered the first hard load — the "always
     * shows Sedang dimuatkan" bug. It must instead ship all three translations so
     * JS can pick the right one from <html lang> every time it is shown.
     */
    public function test_page_loader_ships_all_locale_captions(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-en="Loading..."', $html);
        $this->assertStringContainsString('data-ms="Sedang dimuatkan..."', $html);
        $this->assertStringContainsString('data-zh="加载中..."', $html);
    }
}
