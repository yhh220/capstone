<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\ProductsPage;
use App\Livewire\ProductDetail;
use App\Livewire\AboutPage;
use App\Livewire\ContactPage;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ContactManager;

// Public Routes
Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{slug}', ProductDetail::class)->name('product.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');

// Language switcher — stores locale in session then redirects back
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ms', 'zh'])) {
        session(['locale' => $locale]);
    }
    $referrer = request()->server('HTTP_REFERER', '');
    // Only redirect to same-origin URLs to prevent open-redirect
    $appUrl = rtrim(config('app.url'), '/');
    if ($referrer && str_starts_with($referrer, $appUrl)) {
        return redirect($referrer);
    }
    return redirect()->route('home');
})->name('lang');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/products', ProductManager::class)->name('products');
    Route::get('/categories', CategoryManager::class)->name('categories');
    Route::get('/orders', OrderManager::class)->name('orders');
    Route::get('/contacts', ContactManager::class)->name('contacts');
});
