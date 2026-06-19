<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 'role' is not mass-assignable; set it explicitly via forceFill.
        User::firstOrCreate(
            ['email' => env('DEFAULT_ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'Admin',
                'password' => bcrypt(env('DEFAULT_ADMIN_PASSWORD', 'password')),
            ]
        )->forceFill(['role' => 'owner'])->save();

        // Real categories + products, with their images attached from
        // public/images/products/ (which IS committed to git). Idempotent
        // (updateOrCreate by slug), so `migrate:fresh --seed` gives a fresh
        // teammate the full catalogue with photos in one step.
        $this->call(ProductSeeder::class);

        $this->call(CarModelSeeder::class);
        $this->call(ChatbotFaqSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(FeedbackSeeder::class);

        $services = [
            [
                'name' => 'Car Audio Installation',
                'description' => 'Professional installation of head units, speakers, and amplifiers. Includes wiring and basic system tuning.',
                'price' => 150.00,
                'duration_minutes' => 120,
                'buffer_after' => 15,
                'sort_order' => 1,
            ],
            [
                'name' => 'Subwoofer & Amplifier Setup',
                'description' => 'Full subwoofer and amplifier installation with custom enclosure fitting and tuning for deep, powerful bass.',
                'price' => 200.00,
                'duration_minutes' => 150,
                'buffer_after' => 15,
                'sort_order' => 2,
            ],
            [
                'name' => 'Window Tinting',
                'description' => 'High-quality window film installation for UV protection, heat rejection, and privacy. Full car coverage.',
                'price' => 350.00,
                'duration_minutes' => 180,
                'buffer_after' => 30,
                'sort_order' => 3,
            ],
            [
                'name' => 'Dashcam Installation',
                'description' => 'Clean hidden-wire installation for front and/or rear dashcams. Includes parking mode wiring setup.',
                'price' => 80.00,
                'duration_minutes' => 60,
                'buffer_after' => 15,
                'sort_order' => 4,
            ],
            [
                'name' => 'Car Alarm & Security System',
                'description' => 'Installation of car alarm, immobilizer, or GPS tracker. Protects your vehicle against theft.',
                'price' => 250.00,
                'duration_minutes' => 120,
                'buffer_after' => 15,
                'sort_order' => 5,
            ],
            [
                'name' => 'DSP Tuning & Sound Calibration',
                'description' => 'Professional Digital Sound Processor setup and fine-tuning for audiophile-grade in-car sound quality.',
                'price' => null,
                'duration_minutes' => 90,
                'buffer_after' => 15,
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::firstOrCreate(
                ['name' => $service['name']],
                array_merge($service, ['is_active' => true])
            );
        }

        \Illuminate\Support\Facades\DB::table('settings')->insertOrIgnore([
            ['key' => 'ONLINE_SHOPPING_ENABLED', 'value' => 'false', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_HOURS_START',    'value' => '09:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_HOURS_END',      'value' => '18:00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BUSINESS_CLOSED_WEEKDAYS','value' => '5',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BOOKING_SLOT_MINUTES',    'value' => '30',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'BACKORDER_DAYS',          'value' => '7',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'SHIPPING_FLAT_RATE',      'value' => '10',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'SHIPPING_FREE_THRESHOLD', 'value' => '300',   'created_at' => now(), 'updated_at' => now()],
        ]);

        // Logo files live (committed) in storage/app/public/brand-logos/ — the
        // 'logo' path here references them, so a fresh teammate seed shows the
        // real marquee logos (needs `php artisan storage:link`). Brands without
        // a logo fall back to a styled text label.
        $brands = [
            ['name' => 'Mohawk',  'sort_order' => 1, 'logo' => 'brand-logos/01KV5GMX88MRN42ZP0MXEB5V76.png', 'website_url' => 'https://www.mohawkseries.com/'],
            ['name' => '70mai',   'sort_order' => 2, 'logo' => 'brand-logos/01KV5HTDSH4MTGTN11BXY5CN8B.png', 'website_url' => 'https://www.70mai.com/my/'],
            ['name' => 'Alpine',  'sort_order' => 3, 'logo' => 'brand-logos/01KV5J2C9S97ZZV8BFHHQF7RBP.png', 'website_url' => 'https://alpinemalaysia.com.my/'],
            ['name' => 'Skynavi', 'sort_order' => 4, 'logo' => 'brand-logos/01KV5J8VFAX75NVP1363EVQDAN.jpg', 'website_url' => null],
            ['name' => 'Sparko',  'sort_order' => 5, 'logo' => null,                                          'website_url' => null],
            ['name' => 'SONY',    'sort_order' => 6, 'logo' => 'brand-logos/01KV5JF5D0SV74NRVXF32H9H3S.png', 'website_url' => null],
            ['name' => 'Dynavin', 'sort_order' => 7, 'logo' => 'brand-logos/01KV5JGH348V61T612QPHCCN7Z.png', 'website_url' => 'https://dynavin.com.my/'],
            ['name' => 'MBquart', 'sort_order' => 8, 'logo' => 'brand-logos/01KV5HNYQKGBB5JT6BTJAJV2DH.png', 'website_url' => 'https://mbquart.com/'],
        ];
        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['name' => $brand['name']],
                [
                    'display_type' => $brand['logo'] ? 'image' : 'text',
                    'logo'         => $brand['logo'],
                    'website_url'  => $brand['website_url'],
                    'sort_order'   => $brand['sort_order'],
                    'is_active'    => true,
                ]
            );
        }
    }
}
