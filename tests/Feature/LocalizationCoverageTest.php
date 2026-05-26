<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class LocalizationCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_literal_translation_keys_have_chinese_and_malay_entries(): void
    {
        $keys = [];

        foreach ($this->phpFiles() as $file) {
            $contents = file_get_contents($file->getPathname());

            preg_match_all('/__\(\s*([\'"])(.*?)\1/s', $contents, $matches);

            foreach ($matches[2] as $key) {
                $keys[$key] = true;
            }
        }

        $zh = json_decode(file_get_contents(base_path('lang/zh.json')), true, flags: JSON_THROW_ON_ERROR);
        $ms = json_decode(file_get_contents(base_path('lang/ms.json')), true, flags: JSON_THROW_ON_ERROR);

        $missingZh = array_values(array_diff(array_keys($keys), array_keys($zh)));
        $missingMs = array_values(array_diff(array_keys($keys), array_keys($ms)));

        sort($missingZh);
        sort($missingMs);

        $this->assertSame([], $missingZh, 'Missing zh.json translations.');
        $this->assertSame([], $missingMs, 'Missing ms.json translations.');
    }

    public function test_public_pages_render_in_chinese_and_malay(): void
    {
        foreach (['zh', 'ms'] as $locale) {
            foreach ($this->publicPaths() as $path) {
                $this->withSession(['locale' => $locale])
                    ->get($path)
                    ->assertOk();
            }
        }

        $this->withSession(['locale' => 'zh'])
            ->get('/services')
            ->assertSee('安装服务');

        $this->withSession(['locale' => 'ms'])
            ->get('/services')
            ->assertSee('Servis Pemasangan');
    }

    public function test_error_pages_render_localized_copy(): void
    {
        app()->setLocale('zh');

        $this->get('/definitely-missing-page')
            ->assertNotFound()
            ->assertSee('找不到页面');

        $this->get('/unauthorized')
            ->assertOk()
            ->assertSee('访问被拒绝');

        $this->assertStringContainsString('页面已过期', view('errors.419')->render());
        $this->assertStringContainsString('服务器错误', view('errors.500')->render());

        app()->setLocale('ms');

        $this->assertStringContainsString('Halaman Tamat Tempoh', view('errors.419')->render());
        $this->assertStringContainsString('Ralat Pelayan', view('errors.500')->render());
    }

    private function phpFiles(): iterable
    {
        foreach ([app_path(), resource_path('views'), base_path('routes')] as $path) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            foreach ($iterator as $file) {
                if ($file->isFile() && preg_match('/\.(php|blade\.php)$/', $file->getFilename())) {
                    yield $file;
                }
            }
        }
    }

    private function publicPaths(): array
    {
        return [
            '/',
            '/products',
            '/services',
            '/about',
            '/contact',
            '/booking',
            '/booking/track',
            '/gallery',
            '/track-order',
            '/faq',
            '/privacy-policy',
            '/terms-of-service',
            '/login',
            '/unauthorized',
        ];
    }
}
