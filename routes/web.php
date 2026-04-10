<?php

use App\Livewire\AboutPage;
use App\Livewire\ContactPage;
use App\Livewire\HomePage;
use App\Livewire\ProductDetail;
use App\Livewire\ProductsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{slug}', ProductDetail::class)->name('product.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ms', 'zh'], true)) {
        session(['locale' => $locale]);
    }

    $referrer = request()->server('HTTP_REFERER', '');
    $appUrl = rtrim(config('app.url'), '/');

    if ($referrer && str_starts_with($referrer, $appUrl)) {
        return redirect($referrer);
    }

    return redirect()->route('home');
})->name('lang');
