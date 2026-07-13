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

    public function test_discounted_products_appear_before_full_price_products(): void
    {
        $regular = Product::create([
            'name' => 'Regular Price Product', 'slug' => 'regular-price-product',
            'price' => 100, 'stock' => 10, 'is_active' => true,
        ]);
        $sale = Product::create([
            'name' => 'On Sale Product', 'slug' => 'on-sale-product',
            'price' => 100, 'sale_price' => 80, 'stock' => 10, 'is_active' => true,
        ]);

        Livewire::test(ProductsPage::class)
            ->assertViewHas('products', fn ($products) => $products->first()->is($sale)
                && $products->contains($regular));
    }

    public function test_out_of_stock_products_appear_after_in_stock_products_even_when_discounted(): void
    {
        $inStock = Product::create([
            'name' => 'Available Product', 'slug' => 'available-product',
            'price' => 100, 'stock' => 2, 'is_active' => true,
        ]);
        Product::create([
            'name' => 'Sold Out Sale Product', 'slug' => 'sold-out-sale-product',
            'price' => 100, 'sale_price' => 70, 'stock' => 0, 'is_active' => true,
        ]);

        Livewire::test(ProductsPage::class)
            ->assertViewHas('products', fn ($products) => $products->first()->is($inStock)
                && $products->last()->stock === 0);
    }

    public function test_search_matches_the_translated_name_a_zh_visitor_actually_sees(): void
    {
        // A ZH visitor's cards show name_zh — searching by that visible name has
        // to match, not just the English source columns.
        Product::create([
            'name' => 'Sparko Silicone Wiper Blade Set',
            'name_zh' => 'Sparko 硅胶雨刷套装',
            'slug' => 'sparko-wiper',
            'price' => 55,
            'stock' => 10,
            'is_active' => true,
        ]);

        session(['locale' => 'zh']);
        app()->setLocale('zh');

        Livewire::test(ProductsPage::class)
            ->set('search', '雨刷')
            ->assertSee('Sparko 硅胶雨刷套装');
    }
}
