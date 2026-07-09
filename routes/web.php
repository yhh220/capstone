<?php

use App\Http\Controllers\GmailSendSetupController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Middleware\ShoppingEnabled;
use App\Livewire\AboutPage;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\UserLogin;
use App\Livewire\BookingForm;
use App\Livewire\BookingTracker;
use App\Livewire\CancellationRefundPolicyPage;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\ContactPage;
use App\Livewire\FaqPage;
use App\Livewire\HomePage;
use App\Livewire\MyAccountPage;
use App\Livewire\OrderTracker;
use App\Livewire\PaymentPage;
use App\Livewire\PrivacyPolicyPage;
use App\Livewire\ProductDetail;
use App\Livewire\ProductsPage;
use App\Livewire\ProfilePage;
use App\Livewire\ServicesPage;
use App\Livewire\TermsOfServicePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    Route::get('/pay/{orderNumber}', PaymentPage::class)->name('payment')->middleware('throttle:20,1');
});

// ─── Language Switcher ─────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale, Request $request) {
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

// One-time admin setup: authorize this app to send mail as the store's Gmail
// account via the Gmail API (see GmailApiTransport docblock for why). Admin
// guard only — this hands back an OAuth refresh token, not something a
// customer-facing flow should ever expose.
Route::middleware('auth:admin')->group(function () {
    Route::get('/gmail-send/connect', [GmailSendSetupController::class, 'connect'])
        ->name('gmail-send.connect');
    Route::get('/gmail-send/callback', [GmailSendSetupController::class, 'callback'])
        ->name('gmail-send.callback');
});

// Logout (POST only — CSRF protected)
Route::post('/logout', function () {
    Auth::guard('web')->logout();
    session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// ─── Sitemap ───────────────────────────────────────────────────
Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// ─── Unauthorized Access Page ──────────────────────────────────
Route::get('/unauthorized', fn () => view('errors.unauthorized'))->name('unauthorized');

// ─── Admin Panel ───────────────────────────────────────────────
// Admin dashboard is now powered by Filament and auto-registered
// at /admin via AdminPanelProvider. No manual routes needed.

// ─── Stripe webhook ────────────────────────────────────────────
// Server-to-server payment confirmations from Stripe. Signature-verified in
// the controller and CSRF-exempt (bootstrap/app.php). Deliberately OUTSIDE the
// auth/ShoppingEnabled groups: a payment completing seconds after the owner
// closes the shop must still reach us so the manual-refund alert can fire.
Route::post('/stripe/webhook', StripeWebhookController::class)
    ->middleware('throttle:60,1')
    ->name('stripe.webhook');

// ─── Scheduler trigger (for hosts with no native cron, e.g. Render free tier) ──
// An external pinger (cron-job.org) hits this every minute instead of a real
// crontab running `schedule:run`. Token-gated so it can't be used to spam-run
// scheduled jobs (order expiry, reminder emails) from a guessed public URL.
Route::get('/cron/run-schedule/{token}', function (string $token) {
    if (! hash_equals((string) config('app.cron_secret'), $token)) {
        abort(403);
    }

    Artisan::call('schedule:run');

    return response('OK', 200);
})->name('cron.run-schedule');
