<?php

namespace App\Console\Commands;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml for the public site';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        // Static pages
        $staticPages = [
            ['route' => 'home',          'priority' => '1.0', 'freq' => Url::CHANGE_FREQUENCY_DAILY],
            ['route' => 'products',      'priority' => '0.9', 'freq' => Url::CHANGE_FREQUENCY_DAILY],
            ['route' => 'services',      'priority' => '0.8', 'freq' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['route' => 'gallery',       'priority' => '0.7', 'freq' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['route' => 'booking',       'priority' => '0.8', 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'about',         'priority' => '0.6', 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'contact',       'priority' => '0.6', 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create(route($page['route']))
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['freq'])
                    ->setLastModificationDate(Carbon::now())
            );
        }

        // Product detail pages
        Product::where('is_active', true)->each(function (Product $product) use ($sitemap) {
            $sitemap->add(
                Url::create(route('product.show', $product->slug))
                    ->setPriority('0.7')
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setLastModificationDate($product->updated_at)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $count = Product::where('is_active', true)->count() + count($staticPages);
        $this->info("Sitemap generated with {$count} URLs → public/sitemap.xml");
    }
}
