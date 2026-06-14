<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Android Player' => 'Premium Android players with navigation, Apple CarPlay and Android Auto.',
            'Dash Cam'       => 'Front and rear dash cameras for road safety.',
            'Speaker 6x9'    => '6x9 inch coaxial and component car speakers.',
            'Tweeter'        => 'High-frequency tweeters for crystal clear highs.',
            'Number Plate'   => 'Custom number plates and accessories.',
            'Tinted'         => 'Window tinting films and packages.',
            'Bodykit'        => 'Bodykits, bumpers and aero accessories.',
            'Wiper'          => 'Wiper blades and wiper accessories.',
            'Led Light'      => 'LED headlights, fog lights and interior lighting.',
            'Lamp Cover'     => 'Tail lamp and headlamp covers.',
            'Aircond Gas'    => 'Air conditioning gas refill and service.',
            'Oil Compressor' => 'Compressor oil and AC system lubricants.',
            'Car Wash'       => 'Car wash shampoos, wax and detailing products.',
        ];

        foreach ($categories as $name => $desc) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'        => $name,
                    'description' => $desc,
                    'is_active'   => true,
                ]
            );
        }

        $products = [
            ['Android Player', 'Skynavi', 'Skynavi 9-Inch Octa-Core Android Player', 699, 15, 'Premium 9-inch Android player with wireless Apple CarPlay and Android Auto.', '4GB RAM, 64GB ROM, QLED Screen', 'skynavi_9inch.jpg'],
            ['Android Player', 'Dynavin', 'Dynavin 10.1" Pro Series Android System', 850, 8, 'High-end navigation and entertainment system with DSP audio tuning.', '6GB RAM, 128GB ROM, 4G LTE', 'dynavin_pro.jpg'],
            ['Dash Cam', '70mai', '70mai Dash Cam 4K A810', 599, 30, 'Sony Starvis 2 IMX678 sensor for ultimate 4K video recording, day or night.', '4K UHD, Built-in GPS, ADAS', '70mai_a810.jpg'],
            ['Dash Cam', '70mai', '70mai M300 Dash Cam', 189, 45, '1.5x clearer than 1080P. 140° wide field of view.', '1296P QHD, 3D Noise Reduction', '70mai_m300.jpg'],
            ['Speaker 6x9', 'SONY', 'SONY XS-FB6930 3-Way Coaxial Speakers', 250, 20, 'Mega Bass compatible 3-way speakers for tight, punchy sound.', '450W Max Power, 6x9 inch', 'sony_xsfb6930.jpg'],
            ['Speaker 6x9', 'Alpine', 'Alpine SPJ-691C3 3-Way Coaxial Speaker', 280, 12, 'Entry-level Alpine speakers delivering excellent clarity and bass.', '400W Peak, 60W RMS', 'alpine_spj.jpg'],
            ['Tweeter', 'MBquart', 'MB Quart Premium Silk Dome Tweeters', 150, 25, 'Audiophile grade silk dome tweeters for crisp high-frequency reproduction.', '1-inch Silk Dome, 100W Max', 'mbquart_tweeter.jpg'],
            ['Tweeter', 'Mohawk', 'Mohawk Silver Series Tweeter', 95, 40, 'Budget-friendly high-performance tweeters for any car model.', 'Neo Magnet, Flush Mount', 'mohawk_silver.jpg'],
            ['Number Plate', 'Generic', 'Custom Crystal Acrylic Number Plate', 80, 100, 'JPJ-compliant crystal clear acrylic number plate with 3D fonts.', '3D Font, High Durability', 'noplate_crystal.jpg'],
            ['Tinted', 'Sparko', 'Sparko Premium Ceramic Window Film', 450, 99, 'High heat rejection ceramic tint package (Whole car 4 doors + front/rear).', '95% IR Rejection, 99% UV Rejection', 'sparko_tint.jpg'],
            ['Bodykit', 'Generic', 'Honda Civic FE Type-R Style Bodykit', 1200, 5, 'Complete PP material bodykit for Honda Civic FE (Front, Rear, Side skirts).', 'Polypropylene (PP), Unpainted', 'civic_bodykit.jpg'],
            ['Wiper', 'Sparko', 'Sparko Silicone Wiper Blade Set', 55, 60, 'Water-repellent silicone wipers for silent wiping and clearer vision.', 'Silicone Coating, Aerodynamic', 'sparko_wiper.jpg'],
            ['Led Light', 'Mohawk', 'Mohawk M-Series H4 LED Headlight', 120, 35, 'Super bright white LED headlight bulbs, plug and play installation.', '6500K White, 8000 Lumens', 'mohawk_h4led.jpg'],
            ['Lamp Cover', 'Generic', 'Perodua Myvi Gen3 Smoke Tail Lamp Cover', 85, 15, 'Sporty smoked black tail lamp covers for Perodua Myvi.', 'ABS Plastic, 3M Tape included', 'myvi_lampcover.jpg'],
            ['Aircond Gas', 'Generic', 'Klea R134a Aircond Gas Refill', 45, 50, 'Genuine R134a refrigerant gas for automotive air conditioning systems.', 'R134a, 13.6kg Cylinder (Service)', 'r134a_gas.jpg'],
            ['Oil Compressor', 'Generic', 'Denso ND-Oil 8 Compressor Oil', 60, 30, 'Original Denso PAG46 compressor oil for R134a systems.', 'PAG46, 250ml', 'denso_nd8.jpg'],
        ];

        $imageDir = public_path('images/products');

        foreach ($products as [$catName, $brand, $name, $price, $stock, $desc, $specs, $imageFilename]) {
            $category = Category::where('slug', Str::slug($catName))->first();

            $fullDesc = "{$desc}\n\nSpecifications: {$specs}\nBrand: {$brand}\n\nVisit our showroom to view this product in person or contact us on WhatsApp for recommendations and installation availability.";

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id'       => $category?->id,
                    'brand'             => $brand,
                    'name'              => $name,
                    'short_description' => $desc,
                    'description'       => $fullDesc,
                    'price'             => $price,
                    'sale_price'        => null,
                    // Derive the SKU from the slug so re-seeding keeps it stable
                    // (Str::random() regenerated it on every run before).
                    'sku'               => 'WW-' . strtoupper(substr(md5(Str::slug($name)), 0, 6)),
                    'stock'             => $stock,
                    'is_active'         => true,
                    'is_featured'       => in_array($catName, ['Android Player', 'Dash Cam', 'Speaker 6x9', 'Tweeter', 'Tinted']),
                ]
            );

            // Attach image if it exists and hasn't been attached yet
            $path = "$imageDir/$imageFilename";
            if (File::exists($path)) {
                if ($product->getMedia('images')->isEmpty()) {
                    $product->addMedia($path)
                            ->preservingOriginal()
                            ->toMediaCollection('images');
                }
            }
        }
    }
}
