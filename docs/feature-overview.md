# Win Win Car Audio — Technical Feature Overview

A trilingual (EN / MS / ZH) car-accessories showroom and workshop-booking platform for a
Malaysian car-audio shop, with a customer storefront and a Filament admin panel. Built as an
academic capstone; all sample data is fictional.

> Note on payments: the checkout is a **display-only demonstration** — it shows real Malaysian
> payment methods (FPX, e-wallets, card) but never processes a real charge. No live payment
> gateway is integrated.

---

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3+), Livewire 4.2 (reactive, server-driven UI)
- **Admin:** Filament 5.5 (with the Spatie MediaLibrary plugin)
- **Frontend:** Alpine.js, Tailwind CSS v4 (compiled via Vite 8 — not a CDN), AOS (scroll animations)
- **3D:** Three.js 0.184 with a `@gltf-transform/cli` + meshopt/Draco asset-optimization pipeline
- **Maps:** Leaflet + OpenStreetMap (store-location map)
- **Data:** SQLite (local) → MySQL (production); database-driven queue + Mailable classes for transactional email
- **Packages:** `spatie/laravel-honeypot`, `spatie/laravel-activitylog`, `spatie/laravel-medialibrary`,
  `spatie/laravel-sitemap`, `artesaos/seotools` (per-page meta), `intervention/image-laravel`
- **Tooling:** Pest / PHPUnit (feature tests), Playwright (end-to-end browser tests), Laravel Debugbar (dev), Herd (local host)

---

## Frontend Sitemap

- **Public:** Home, Products, Product detail, Services, Booking, About, Contact, FAQ, Privacy Policy, Terms of Service
- **Commerce:** Cart + mini-cart drawer, Checkout / Payment, My Orders (history), Profile / Account (with preferred-courier selection)
- **Auth:** Login & Registration (with a password-strength meter)
- **Interactive:** 3D Configurator, **vehicle-compatibility checker**, Booking-status lookup, Order/Shipping-status lookup, Chatbot, page preloader, store Leaflet map, skip-to-content link
- **System:** dynamic `sitemap.xml`, custom error/status pages (404 / 419 / 429 / 500 / 503 / unauthorized)
- **Two browsing modes:** View-only (showroom) and Online Shopping (cart + checkout), switched by an admin setting (see *Online Shopping Mode* below)

---

## Admin Panel (Filament)

Thirteen resources, each with List/Create/Edit screens unless noted:

- **Shop → Orders** — List + **Edit only** (orders originate from checkout; status advances pending → processing → shipped → delivered).
- **Store Products → Brands, Categories, Products** — Products carry multilingual descriptions, price + sale price (validated `sale_price ≤ price`), stock 0–999 with backorder, specs, compatible vehicles, a 3D-model URL, and media-library image upload with an image editor.
- **Services & Bookings → Services, Bookings** — Services support drag-and-drop ordering and a per-service icon; Bookings expose status management and time-slot data.
- **Customer Interactions → Contacts, Customers, Testimonials (Feedback)** — inbound messages, customer records, review moderation.
- **System Settings → Activities (audit log), Users** — Activities is **read-only with a View page** (spatie/activitylog viewer); Users manages admin/staff accounts.
- **System → Chatbot FAQs, Settings** — FAQs carry a priority and multilingual replies and bust the chatbot cache on save; Settings is the key/value config store.

**Dashboard widgets:** StatsOverview (KPI cards), RevenueChart, CategoryDistributionChart, TopProductsChart, RecentOrdersWidget, RecentActivityWidget.

### Online Shopping Mode (admin-controlled feature flag)

The View-only ↔ Online-Shopping switch is operated entirely from the admin, not code:

- A key/value setting `ONLINE_SHOPPING_ENABLED` (`"true"` / `"false"`) in **System → Settings**.
- Server-side enforcement: cart/checkout routes are grouped behind `['auth', ShoppingEnabled::class]`
  middleware; when the flag isn't `"true"`, the middleware redirects to Home with a "coming soon" message,
  so the routes are unreachable even by direct URL.
- The same flag is read across HomePage, ProductsPage, ProductDetail and the layout to hide prices,
  the "Add to Cart" button, the price filter, and the cart badge — turning the site into a pure showroom.

---

## Admin Authentication & Authorization

**Authentication**

- The admin panel lives at `/admin` and authenticates against a **separate `admin` guard** (`->authGuard('admin')`), so admin sessions are isolated from the storefront `web` guard.
- A custom Filament Login page (`App\Filament\Pages\Auth\Login`) extends Filament's base login and inherits its built-in login throttling.
- The panel stack includes `AuthenticateSession` (session-integrity — invalidates other sessions on password change, guards against fixation) and `authMiddleware([Authenticate::class])` requiring a logged-in user for every panel route.
- A dedicated `LogoutAdminGuardOnly` middleware logs out only the admin guard, so signing out of the panel doesn't destroy a separate storefront session.

**Authorization (role-based access control)**

- Users carry a `role` column with four tiers: `owner`, `admin`, `staff`, `client` (default `client`).
- Panel access is gated by `canAccessPanel()`: only owner / admin / staff pass; clients are rejected at the panel boundary even if authenticated.
- Role helpers — `isOwner()`, `isAdmin()` (owner counts as admin), `isStaff()`, `isClient()` — centralise the checks.
- A separate `AdminMiddleware` protects custom admin routes: it requires `isAdmin()`, force-logs-out any non-admin that reaches the route, and redirects to the admin login with an "Unauthorized access" message.
- **Privilege-escalation protection:** `role` is intentionally excluded from mass assignment (set only in code/seeders), so a user can never elevate their own role via form input.

**Storefront authentication (for contrast)** — the customer-facing `UserLogin` is a separate Livewire flow with bcrypt (`'hashed'` cast), progressive lockout tiers (`LOCKOUT_TIERS`) + IP banning, and a honeypot. The project therefore runs **two independent auth systems**: customer (storefront) and admin/staff (Filament panel).

---

## Core Features

- **Trilingual i18n (EN/MS/ZH)** enforced by `LocalizationCoverageTest` (fails on any missing translation key); `SetLocale` middleware resolves the active locale per request from the session.
- **Trilingual chatbot:** typo tolerance (Levenshtein distance), slang normalization, Traditional→Simplified Chinese conversion, per-message language auto-detection; FAQs are admin-editable and cached.
- **Vehicle-compatibility checker:** pick a car brand → model and see which products fit (CarModel + ProductCompatibility data).
- **Guest-accessible reference lookup:** order/booking verified by reference number + phone/email (no login required).
- **WhatsApp pre-filled message** redirect across the storefront.
- **Flash-free light/dark mode:** the server reads the `app_theme` cookie and renders `class="dark"` server-side (no FOUC).
- **Backorder / pre-order** with oversell/negative-stock locking.
- **Reactive cart:** `cart-updated` events drive a live badge count and add-to-cart feedback.
- **Booking time-slot engine:** per-day slot generation, rest-day handling, buffer windows.
- **Audit logging** (spatie/activitylog across the core models) and a **media library** for product images.
- **3D asset optimization:** cache-busting, on-demand rendering, dynamic resolution, baked shadow maps.
- **Settings-driven content** (store hours, phone, shopping mode, etc. via DB Settings + a `setting()` helper).
- **SEO:** per-page meta (title, description) via `artesaos/seotools` + a `SetsSeo` concern, favicon, and a dynamic `sitemap.xml`.

---

## Payment (display-only demonstration)

- The checkout shows the **real Malaysian payment landscape** — FPX online banking (with a bank picker),
  e-wallets (Touch 'n Go, GrabPay, ShopeePay, Boost), and credit/debit card — plus Cash on Delivery.
- **No real charge occurs.** Card fields are display-only and never bound, collected, or stored; a
  `wire:confirm` prompt states it's a demo before placing the test order.
- The selected provider is whitelisted server-side and stored as a readable label (e.g. `FPX - Maybank2u`)
  on the order. The order is created with `payment_status = pending`.

---

## Security

- **Full security-header suite:** CSP (`default-src 'self'`, `object-src 'none'`, `frame-ancestors`, `base-uri`, `form-action`), HSTS, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, `Permissions-Policy` (camera/mic/geolocation/payment disabled), and removal of `X-Powered-By` / `Server` fingerprints.
- **Progressive login lockout + IP banning** (`LOCKOUT_TIERS` — escalating, not flat).
- **Open-redirect protection** (the language-switch route accepts same-origin targets only).
- **Duplicate-submission guard** on Contact / Booking forms.
- **bcrypt password hashing** (`'hashed'` cast) · **CSRF protection** (Laravel + Livewire token).
- **SQL-injection prevention** (Eloquent ORM / parameterized queries) · **mass-assignment protection** (`Fillable` allowlists; `role` excluded).
- **Secure session cookies** (HttpOnly, SameSite=Lax, Secure under HTTPS).
- **Rate limiting** (per-IP burst + daily caps on contact, booking, tracking, chatbot, checkout) · **honeypot** spam trap · **server-side authorization** middleware.
- **XSS protection** (Blade auto-escaping + `strip_tags`) and **input validation** across Livewire + Filament.

---

## Reliability (mechanism, not buzzword)

- **Caching** → `Cache::remember` (DB-backed; e.g. dashboard stats, FAQs).
- **Cache Invalidation** → model `saved`/`deleted` events bust keys.
- **Concurrency Limit** → `DB::transaction` + `lockForUpdate` (oversell prevention).
- **Fail-fast + Circuit Breaker** → `try/catch` degradation (chatbot → WhatsApp fallback), 3D `AbortController`.
- **Request Timeouts** → 30s `AbortController` on the GLB stream.
- **Resource Leak Prevention** → Three.js `dispose()` + `cancelAnimationFrame`.
- **Non-blocking Requests** → two-phase Livewire dispatch + queued mail.
- **Stale-response Race Guard** → `isLoading` / `pendingText` guard in the chatbot.
- **Per-row Error Isolation** → per-message `try/catch`, per-FAQ filter.
- **Graceful Degradation** → `isWebGLAvailable` fallback, `@error` blocks.

---

## UX

- Page preloader, tooltips, skeleton loaders, custom error/status pages.
- Flash-free dark mode, reactive cart, password-strength meter.
- Empty states (no orders / no results), `wire:loading` states, mobile-first responsive design, trilingual copy.

---

## Accessibility (aligned with WCAG 2.1 success criteria)

- **Programmatic label association** — every control bound to its `<label>` via `for`/`id` (SC 1.3.1, 4.1.2).
- **Text alternatives** — `alt` on content images; decorative imagery hidden with `alt="" + aria-hidden="true"` (SC 1.1.1).
- **Accessible names for icon-only controls** — `aria-label` on icon buttons (password toggle, cart, language switch) (SC 4.1.2).
- **ARIA landmarks** — `<nav> <main> <header> <footer> <aside>` for region navigation (SC 1.3.1, 2.4.1).
- **Skip-to-content link** — bypasses navigation to `#main-content` (SC 2.4.1).
- **Live regions / status messages** — `aria-live="polite"`, `role="alert"` (SC 4.1.3).
- **Programmatic page language** — `<html lang>` per active locale, switches EN/MS/ZH (SC 3.1.1).
- **Input-purpose identification** — `autocomplete` tokens (name/email/tel/street-address/postal-code) (SC 1.3.5).
- **Reduced-motion support** — `@media (prefers-reduced-motion: reduce)` disables non-essential animation (SC 2.3.3).
- **Visible focus indicators** — `:focus` / `focus:ring` keyboard focus styling (SC 2.4.7).
- **Keyboard operability** — all interactive controls keyboard-reachable and operable (SC 2.1.1).
- **Destructive-action confirmation** — `wire:confirm` double-confirm (SC 3.3.4).
- **Tabular figures** — `tabular-nums` for column-aligned monetary values.

> Stated as "aligned with WCAG 2.1 success criteria" rather than "WCAG 2.1 AA compliant", since no formal audit has been run.

---

## Scheduled Tasks & Data Retention

- `sitemap:generate` runs **daily** to refresh `sitemap.xml`.
- `model:prune` runs **daily** on `ChatLog`, so chatbot logs are auto-pruned (keeping the database and the "this month" dashboard stats clean).
- Both rely on Laravel's scheduler (`routes/console.php`); transactional email relies on the queue worker (or `QUEUE_CONNECTION=sync` in production).

---

## Testing

- **Feature tests** (Pest / PHPUnit) covering checkout (locked cart, backorder), services page, localization coverage, and error-page rendering.
- **`LocalizationCoverageTest`** programmatically guarantees every `__()` key has EN/MS/ZH entries.
- **Playwright** end-to-end browser tests for interactive flows.
