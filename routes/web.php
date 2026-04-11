<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\ProductsPage;
use App\Livewire\ProductDetail;
use App\Livewire\AboutPage;
use App\Livewire\ContactPage;
use App\Livewire\Auth\UserLogin;
use App\Livewire\Auth\AdminLogin;

// ─── Public Routes ─────────────────────────────────────────────
Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{slug}', ProductDetail::class)->name('product.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');

// ─── Language Switcher ─────────────────────────────────────────
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

// ─── Authentication Routes ─────────────────────────────────────
Route::get('/login', UserLogin::class)->name('login');
Route::get('/admin/login', AdminLogin::class)->name('admin.login');

// Logout (POST only — CSRF protected)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// ─── Admin Panel ───────────────────────────────────────────────
// Admin dashboard is now powered by Filament and auto-registered
// at /admin via AdminPanelProvider. No manual routes needed.
