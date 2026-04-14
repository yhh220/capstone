<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\ProductsPage;
use App\Livewire\ProductDetail;
use App\Livewire\AboutPage;
use App\Livewire\ContactPage;
use App\Livewire\ServicesPage;
use App\Livewire\BookingForm;
use App\Livewire\BookingTracker;
use App\Livewire\GalleryPage;
use App\Livewire\Auth\UserLogin;

// ─── Public Routes ─────────────────────────────────────────────
Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{slug}', ProductDetail::class)->name('product.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/services', ServicesPage::class)->name('services');
Route::get('/booking', BookingForm::class)->name('booking');
Route::get('/booking/track', BookingTracker::class)->name('booking.track');
Route::get('/gallery', GalleryPage::class)->name('gallery');

// ─── Language Switcher ─────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ms', 'zh'], true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('lang');

// ─── Authentication Routes ─────────────────────────────────────
Route::get('/login', UserLogin::class)->name('login');

// Logout (POST only — CSRF protected)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// ─── Sitemap ───────────────────────────────────────────────────
Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (!file_exists($path)) {
        \Artisan::call('sitemap:generate');
    }
    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// ─── Admin Panel ───────────────────────────────────────────────
// Admin dashboard is now powered by Filament and auto-registered
// at /admin via AdminPanelProvider. No manual routes needed.
