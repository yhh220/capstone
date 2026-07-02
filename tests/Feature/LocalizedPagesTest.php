<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * End-to-end checks that the storefront actually renders in the visitor's
 * language: DB-driven content (service names, category names, product name
 * translations), Carbon dates, and framework validation messages — the three
 * things that used to stay English no matter the locale.
 */
class LocalizedPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_page_renders_service_and_calendar_in_chinese(): void
    {
        Service::create([
            'name' => 'Car Audio Installation',
            'description' => 'Professional installation of head units, speakers, and complete sound systems.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Step 1: service cards translate via __($svc->name).
        $this->withSession(['locale' => 'zh'])
            ->get('/booking')
            ->assertOk()
            ->assertSee('汽车音响安装');

        // Step 2 (calendar): month label localizes via the Carbon locale sync.
        // setLocale() fires LocaleUpdated, which is exactly the hook under test.
        app()->setLocale('zh');
        $expectedMonth = now()->translatedFormat('F Y'); // e.g. 七月 2026

        \Livewire\Livewire::test(\App\Livewire\BookingForm::class)
            ->set('currentStep', 2)
            ->assertSee($expectedMonth);
    }

    public function test_products_page_renders_translated_name_and_category_in_chinese(): void
    {
        $cat = Category::create(['name' => 'Wiper', 'slug' => 'wiper', 'is_active' => true]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Sparko Silicone Wiper Blade Set',
            'name_zh' => 'Sparko 硅胶雨刷套装',
            'slug' => 'sparko-wiper',
            'price' => 55,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->withSession(['locale' => 'zh'])
            ->get('/products')
            ->assertOk()
            ->assertSee('Sparko 硅胶雨刷套装') // name_zh via translated_name accessor
            ->assertSee('雨刮器');             // category "Wiper" via __() + zh.json
    }

    public function test_product_name_falls_back_to_english_when_no_translation(): void
    {
        $cat = Category::create(['name' => 'Wiper', 'slug' => 'wiper', 'is_active' => true]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Sparko Silicone Wiper Blade Set',
            'slug' => 'sparko-wiper',
            'price' => 55,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->withSession(['locale' => 'zh'])
            ->get('/products')
            ->assertOk()
            ->assertSee('Sparko Silicone Wiper Blade Set');
    }

    public function test_framework_validation_messages_translate(): void
    {
        app()->setLocale('zh');
        $v = Validator::make(['preferred_date' => ''], ['preferred_date' => 'required']);
        $this->assertSame('预约日期 不能为空。', $v->errors()->first('preferred_date'));

        app()->setLocale('ms');
        $v = Validator::make(['customer_name' => ''], ['customer_name' => 'required']);
        $this->assertSame('nama wajib diisi.', $v->errors()->first('customer_name'));
    }
}
