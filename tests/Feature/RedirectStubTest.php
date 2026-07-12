<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The old Render service becomes a lightweight redirect stub when REDIRECT_TO is
 * set (its printed-QR domain must keep working after the real site moved). These
 * pin both modes: stub redirects everything preserving path+query; the real site
 * (no REDIRECT_TO) is completely unaffected.
 */
class RedirectStubTest extends TestCase
{
    // The "real site unaffected" case renders the actual homepage, which reads
    // several tables; the redirect cases short-circuit before any DB touch.
    use RefreshDatabase;


    public function test_stub_redirects_the_homepage_to_the_new_host(): void
    {
        config(['app.redirect_to' => 'https://winwinautoaccessories.onrender.com']);

        $this->get('/')
            ->assertStatus(302)
            ->assertRedirect('https://winwinautoaccessories.onrender.com/');
    }

    public function test_stub_preserves_the_path_and_query_string(): void
    {
        config(['app.redirect_to' => 'https://winwinautoaccessories.onrender.com']);

        $this->get('/products?category=2&sort=price')
            ->assertStatus(302)
            ->assertRedirect('https://winwinautoaccessories.onrender.com/products?category=2&sort=price');
    }

    public function test_a_trailing_slash_on_the_target_is_not_doubled(): void
    {
        config(['app.redirect_to' => 'https://winwinautoaccessories.onrender.com/']);

        $this->get('/about')
            ->assertRedirect('https://winwinautoaccessories.onrender.com/about');
    }

    public function test_real_site_is_unaffected_when_redirect_to_is_unset(): void
    {
        config(['app.redirect_to' => null]);

        // The real storefront homepage renders normally — no redirect.
        $this->get('/')->assertOk();
    }
}
