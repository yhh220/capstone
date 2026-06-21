<?php

namespace Tests\Feature;

use App\Livewire\ProductsPage;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductsPageSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_term_containing_percent_sign_still_matches(): void
    {
        Product::create([
            'name' => '50% Tint Film',
            'slug' => '50-tint-film',
            'price' => 80,
            'stock' => 10,
            'is_active' => true,
        ]);

        Livewire::test(ProductsPage::class)
            ->set('search', '50%')
            ->assertSee('50% Tint Film');
    }

    public function test_search_term_containing_underscore_still_matches(): void
    {
        Product::create([
            'name' => 'Cable Set',
            'sku' => 'ABC_123',
            'slug' => 'cable-set',
            'price' => 20,
            'stock' => 10,
            'is_active' => true,
        ]);

        Livewire::test(ProductsPage::class)
            ->set('search', 'ABC_123')
            ->assertSee('Cable Set');
    }
}
