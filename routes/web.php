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
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ContactManager;

// ─── Public Routes ─────────────────────────────────────────────
Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{slug}', ProductDetail::class)->name('product.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');

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

// ─── Admin Routes (Protected by AdminMiddleware) ───────────────
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/products', ProductManager::class)->name('products');
    Route::get('/categories', CategoryManager::class)->name('categories');
    Route::get('/orders', OrderManager::class)->name('orders');
    Route::get('/contacts', ContactManager::class)->name('contacts');
});
