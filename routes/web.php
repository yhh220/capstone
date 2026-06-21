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
use App\Livewire\FaqPage;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\OrderTracker;
use App\Livewire\PrivacyPolicyPage;
use App\Livewire\ProfilePage;
use App\Livewire\TermsOfServicePage;
use App\Livewire\CancellationRefundPolicyPage;
use App\Livewire\MyAccountPage;
use App\Livewire\PaymentPage;
use App\Livewire\Auth\UserLogin;
use App\Livewire\Auth\ForgotPassword;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Middleware\ShoppingEnabled;

// ─── Public Routes ─────────────────────────────────────────────
Route::get('/', HomePage::class)->name('home');
Route::get('/products', ProductsPage::class)->name('products');
Route::get('/products/{slug}', ProductDetail::class)->name('product.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/services', ServicesPage::class)->name('services');
Route::get('/booking', BookingForm::class)->name('booking');
Route::get('/booking/track', BookingTracker::class)->name('booking.track');
Route::get('/track-order', OrderTracker::class)->name('track-order');
Route::get('/faq', FaqPage::class)->name('faq');
Route::get('/privacy-policy', PrivacyPolicyPage::class)->name('privacy-policy');
Route::get('/terms-of-service', TermsOfServicePage::class)->name('terms-of-service');
Route::get('/cancellation-refund-policy', CancellationRefundPolicyPage::class)->name('cancellation-refund-policy');

// ─── Authenticated User Routes ────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', ProfilePage::class)->name('profile');
    Route::get('/account', MyAccountPage::class)->name('account');
});
// Invoices — owner (web guard) or admin/staff (admin guard) may view.
// Throttled (matches OrderTracker's own limit) so the 404/403/200 split
// across order numbers can't be used to enumerate which ones are real.
Route::middleware(['auth:web,admin', 'throttle:5,1'])->group(function () {
    Route::get('/orders/{orderNumber}/invoice', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/orders/{orderNumber}/invoice/pdf', [InvoiceController::class, 'download'])->name('invoice.pdf');
});
// Legacy path → unified account page.
Route::redirect('/my-orders', '/account');
// ─── Shopping Routes (protected by ShoppingEnabled + auth middleware) ──
Route::middleware(['auth', ShoppingEnabled::class])->group(function () {
    Route::get('/cart', CartPage::class)->name('cart');
    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    Route::get('/pay/{orderNumber}', PaymentPage::class)->name('payment');
});

// ─── Language Switcher ─────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale, \Illuminate\Http\Request $request) {
    if (in_array($locale, ['en', 'ms', 'zh'], true)) {
        session(['locale' => $locale]);
    }

    // Open-redirect guard: only return to a same-origin previous URL.
    $previous = url()->previous();
    $sameHost = $previous && parse_url($previous, PHP_URL_HOST) === $request->getHost();

    return redirect($sameHost ? $previous : '/');
})->name('lang');

// ─── Authentication Routes ─────────────────────────────────────
Route::get('/login', UserLogin::class)->name('login');
Route::get('/forgot-password', ForgotPassword::class)->name('password.request');

// Social login (OAuth). The controller 404s any provider that isn't configured.
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->whereIn('provider', ['google', 'microsoft'])
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->whereIn('provider', ['google', 'microsoft'])
    ->name('social.callback');

// Logout (POST only — CSRF protected)
Route::post('/logout', function () {
    Auth::guard('web')->logout();
    session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// ─── Sitemap ───────────────────────────────────────────────────
Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// ─── Unauthorized Access Page ──────────────────────────────────
Route::get('/unauthorized', fn () => view('errors.unauthorized'))->name('unauthorized');

// ─── Admin Panel ───────────────────────────────────────────────
// Admin dashboard is now powered by Filament and auto-registered
// at /admin via AdminPanelProvider. No manual routes needed.
