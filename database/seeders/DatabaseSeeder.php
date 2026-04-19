<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('DEFAULT_ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'Admin',
                'password' => bcrypt(env('DEFAULT_ADMIN_PASSWORD', 'password')),
                'role' => 'owner',
            ]
        );

        $categories = [
            ['name' => 'Seat Covers', 'description' => 'Premium seat covers for all car models'],
            ['name' => 'Audio Systems', 'description' => 'Car audio and entertainment systems'],
            ['name' => 'Dash Cameras', 'description' => 'Front and rear dash cameras'],
            ['name' => 'LED Lighting', 'description' => 'Interior and exterior LED lights'],
            ['name' => 'Car Mats', 'description' => 'Floor mats and trunk liners'],
            ['name' => 'Air Fresheners', 'description' => 'Car air fresheners and diffusers'],
            ['name' => 'Phone Holders', 'description' => 'Dashboard and vent phone mounts'],
            ['name' => 'Steering Covers', 'description' => 'Steering wheel covers and accessories'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'is_active' => true,
            ]);
        }

        $products = [
            ['name' => 'Premium Leather Seat Cover Set', 'category' => 'Seat Covers', 'price' => 189.00, 'sale_price' => 159.00, 'stock' => 50, 'featured' => true, 'short' => 'Full set leather seat covers, fits most sedan models. Water resistant and easy to clean.'],
            ['name' => 'Universal Car Floor Mats 5pcs', 'category' => 'Car Mats', 'price' => 79.90, 'sale_price' => null, 'stock' => 100, 'featured' => true, 'short' => 'Durable rubber floor mats, anti-slip design. Universal fit for most car models.'],
            ['name' => '4K Dual Dash Camera', 'category' => 'Dash Cameras', 'price' => 349.00, 'sale_price' => 299.00, 'stock' => 30, 'featured' => true, 'short' => '4K front + 1080p rear dash cam with night vision, GPS, and 128GB SD card support.'],
            ['name' => 'Pioneer Car Audio Head Unit 7"', 'category' => 'Audio Systems', 'price' => 599.00, 'sale_price' => null, 'stock' => 15, 'featured' => true, 'short' => '7-inch touchscreen head unit with Apple CarPlay, Android Auto, Bluetooth.'],
            ['name' => 'RGB Interior LED Strip Light Kit', 'category' => 'LED Lighting', 'price' => 49.90, 'sale_price' => 39.90, 'stock' => 200, 'featured' => true, 'short' => '16 million colors RGB LED strip with remote control and app support.'],
            ['name' => 'Luxury Steering Wheel Cover', 'category' => 'Steering Covers', 'price' => 39.90, 'sale_price' => null, 'stock' => 75, 'featured' => true, 'short' => 'Genuine leather steering wheel cover with anti-slip grip. Universal fit 37-38cm.'],
            ['name' => 'Magnetic Car Phone Holder', 'category' => 'Phone Holders', 'price' => 29.90, 'sale_price' => 24.90, 'stock' => 150, 'featured' => false, 'short' => 'Strong magnetic phone mount for dashboard or vent. Compatible with all phones.'],
            ['name' => 'Wooden Scent Air Freshener Set 3pcs', 'category' => 'Air Fresheners', 'price' => 19.90, 'sale_price' => null, 'stock' => 300, 'featured' => false, 'short' => 'Natural wood car air freshener in 3 scents. Long lasting fragrance up to 60 days.'],
            ['name' => 'Fabric Seat Cover Set (Full)', 'category' => 'Seat Covers', 'price' => 119.00, 'sale_price' => null, 'stock' => 40, 'featured' => false, 'short' => 'Breathable fabric seat cover, 11-piece full set including headrest and armrest covers.'],
            ['name' => 'Mini Hidden Dash Camera', 'category' => 'Dash Cameras', 'price' => 129.00, 'sale_price' => 109.00, 'stock' => 45, 'featured' => false, 'short' => 'Discreet mini dash cam with 1080p recording, loop recording, motion detection.'],
            ['name' => 'Subwoofer 10" 1000W', 'category' => 'Audio Systems', 'price' => 279.00, 'sale_price' => null, 'stock' => 20, 'featured' => false, 'short' => 'Powerful 10-inch subwoofer with 1000W peak power. Includes mounting box.'],
            ['name' => 'LED Headlight Bulb H4 Pair', 'category' => 'LED Lighting', 'price' => 89.90, 'sale_price' => 69.90, 'stock' => 80, 'featured' => false, 'short' => 'Super bright H4 LED headlight bulbs, 6000K white, 12000LM total output.'],
        ];

        foreach ($products as $product) {
            $category = Category::where('name', $product['category'])->first();

            Product::create([
                'category_id' => $category?->id,
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'short_description' => $product['short'],
                'description' => $product['short'] . "\n\nVisit our showroom to view this product in person or contact us on WhatsApp for recommendations and installation availability.",
                'price' => $product['price'],
                'sale_price' => $product['sale_price'],
                'sku' => 'WW-' . strtoupper(Str::random(6)),
                'stock' => $product['stock'],
                'is_active' => true,
                'is_featured' => $product['featured'],
            ]);
        }

        $this->call(CarModelSeeder::class);

        foreach ([
            ['name' => 'Ahmad Rizal', 'location' => 'KL', 'message' => "The staff explained the options clearly on WhatsApp before I came over. The showroom visit was smooth and helpful.", 'rating' => 5, 'sort_order' => 1],
            ['name' => 'Siti Nurul', 'location' => 'Selangor', 'message' => 'Very helpful team and excellent product guidance. I could compare models in person before deciding.', 'rating' => 5, 'sort_order' => 2],
            ['name' => 'Tan Wei Ming', 'location' => 'Penang', 'message' => 'I liked that the website showed the products first, then the store team helped me choose the right fit.', 'rating' => 5, 'sort_order' => 3],
        ] as $feedback) {
            Feedback::create([
                'name' => $feedback['name'],
                'location' => $feedback['location'],
                'message' => $feedback['message'],
                'rating' => $feedback['rating'],
                'is_active' => true,
                'sort_order' => $feedback['sort_order'],
            ]);
        }

        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            ['key' => 'ONLINE_SHOPPING_ENABLED', 'value' => 'false', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_HOURS_START',    'value' => '09:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_HOURS_END',      'value' => '18:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
