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

    public function test_contact_page_loads_leaflet_and_the_map(): void
    {
        $html = $this->get('/contact')->assertOk()->getContent();

        $this->assertStringContainsString('leaflet.js', $html, 'Contact needs Leaflet JS for its map.');
        $this->assertStringContainsString('leaflet.css', $html, 'Contact needs Leaflet CSS for its map.');
        $this->assertStringContainsString('store-map', $html);

        // The map init must run after L is available: leaflet.js appears before
        // the L.map( call in the document order.
        $this->assertLessThan(
            strpos($html, 'L.map('),
            strpos($html, 'leaflet.js'),
            'Leaflet JS must be emitted before the map initialisation script.'
        );
    }

    public function test_home_page_does_not_load_leaflet(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('leaflet', $html, 'Pages without a map must not load Leaflet.');
    }
}
