<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_page_renders_successfully(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('Installation Services');
    }
}
