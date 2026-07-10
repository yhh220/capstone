# Win Win Car Audio — System Audit

> **Purpose:** Complete feature & implementation inventory for the Capstone Report.
> **Method:** Every entry below was verified by scanning the codebase (file paths given). Items that do not exist are explicitly marked **Not implemented** / **Not found**.
> **Generated:** 2026-06-26 · **Last updated:** 2026-07-04 (deep security + UX audit + shop-mode graceful shutdown — see §49)
> **Live site (production):** https://winwincaraudio.onrender.com · **Admin panel:** https://winwincaraudio.onrender.com/admin
> **Repository:** https://github.com/yhh220/capstone
> **Stack at a glance:** Laravel 13 · Livewire 4 · Filament 5 · PHP 8.4 · Tailwind CSS 4 (Vite) · Three.js · TiDB (prod) / SQLite (local) · Hosted on Render

---

## Table of Contents
1. Functional Features
2. UI/UX Design Details
3. Non-Functional Requirements (NFR)
4. Security Implementation
5. Database Schema
6. Technology Stack
7. Third-party Integrations
8. Project File Structure
9. Known Gaps / TODO
10. Interactive 3D Configurator
11. E-Commerce & Cart System
12. Authentication & User Management
13. Product Catalog Features
14. Services & Booking System
15. Multimedia & Visual Assets
16. Theme System (Dark/Light)
17. Animations & Micro-interactions
18. Static / Legal Pages
19. Contact Integrations
20. Multi-Mode (Shop Mode) Features
21. SEO Implementation
22. Deployment & DevOps
23. Browser & Device Compatibility
24. Form Handling Patterns
25. Admin Panel (Filament) — Full Detail
26. Version Control, Tooling & Collaboration
29. Keyword-Based Chatbot · 31. Laravel Application Layer · 32. Email & Notifications · 33. Caching · 34. Search · 35. Error Handling · 36. Performance · 37. Cookie/Privacy · 38. Analytics · 39. CX Enhancements · 40. Testing & Code Quality · 41. Documentation · 42. Navigation · 43. CTA Strategy · 44. Content Management · 45. DB & Migrations · 46. Routes · 47. Localization · 48. Final QA Checklist · 49. Deep Security & UX Audit (2 Jul 2026)

---

# 1. Functional Features

## 1.1 Customer-facing features (storefront)

All public routes are registered in [routes/web.php](../routes/web.php) and rendered by full-page Livewire components in [app/Livewire/](../app/Livewire/).

| Feature | Route / File | Description | DB Tables | Audience |
|---|---|---|---|---|
| Home page | `/` → [HomePage.php](../app/Livewire/HomePage.php) | Hero video, featured products, brand marquee, services preview, testimonials | `products`, `brands`, `feedback`, `services`, `settings` | Customer |
| Product catalog | `/products` → [ProductsPage.php](../app/Livewire/ProductsPage.php) | Searchable, category-filtered product grid with pagination | `products`, `categories` | Customer |
| Product detail | `/products/{slug}` → [ProductDetail.php](../app/Livewire/ProductDetail.php) | Full product info, specs, translated overview, related products, add-to-cart | `products`, `categories` | Customer |
| About | `/about` → [AboutPage.php](../app/Livewire/AboutPage.php) | Company introduction | — | Customer |
| Contact | `/contact` → [ContactPage.php](../app/Livewire/ContactPage.php) | Contact form + map + business info | `contacts` | Customer |
| Services | `/services` → [ServicesPage.php](../app/Livewire/ServicesPage.php) | List of installation/service offerings | `services` | Customer |
| Booking | `/booking` → [BookingForm.php](../app/Livewire/BookingForm.php) | Multi-step appointment booking with calendar + time slots | `bookings`, `services` | Customer |
| Booking tracker | `/booking/track` → [BookingTracker.php](../app/Livewire/BookingTracker.php) | Look up a booking by reference | `bookings` | Customer |
| Order tracker | `/track-order` → [OrderTracker.php](../app/Livewire/OrderTracker.php) | Look up an order by order number (throttled) | `orders`, `order_items` | Customer |
| FAQ | `/faq` → [FaqPage.php](../app/Livewire/FaqPage.php) | Accordion of published FAQs | `faqs` | Customer |
| Cart | `/cart` → [CartPage.php](../app/Livewire/CartPage.php) | Cart line management, subtotal/shipping/total (Shop-Mode gated) | `cart_items`, `products` | Customer |
| Checkout | `/checkout` → [CheckoutPage.php](../app/Livewire/CheckoutPage.php) | Shipping details + order placement (Shop-Mode gated) | `orders`, `order_items`, `users` | Customer |
| Payment | `/pay/{orderNumber}` → [PaymentPage.php](../app/Livewire/PaymentPage.php) | Simulated FPX/e-wallet/card payment screen w/ 15-min expiry countdown (throttled) | `orders` | Customer |
| My Account | `/account` → [MyAccountPage.php](../app/Livewire/MyAccountPage.php) | Order history + booking history dashboard | `orders`, `bookings` | Customer (auth) |
| Profile | `/profile` → [ProfilePage.php](../app/Livewire/ProfilePage.php) | Edit profile, change/set password, toggle 2FA, delete account | `users`, `social_accounts` | Customer (auth) |
| Privacy Policy | `/privacy-policy` → [PrivacyPolicyPage.php](../app/Livewire/PrivacyPolicyPage.php) | Legal text | — | Customer |
| Terms of Service | `/terms-of-service` → [TermsOfServicePage.php](../app/Livewire/TermsOfServicePage.php) | Legal text | — | Customer |
| Cancellation/Refund | `/cancellation-refund-policy` → [CancellationRefundPolicyPage.php](../app/Livewire/CancellationRefundPolicyPage.php) | Legal text (refund window driven by settings) | `settings` | Customer |
| Invoice (HTML) | `/orders/{orderNumber}/invoice` → [InvoiceController.php](../app/Http/Controllers/InvoiceController.php) | View invoice (auth:web,admin, throttle 5/min) | `orders`, `order_items` | Customer (auth) / Admin |
| Invoice (PDF) | `/orders/{orderNumber}/invoice/pdf` → InvoiceController | Download dompdf-rendered PDF invoice | `orders`, `order_items` | Customer (auth) / Admin |
| Language switcher | `/lang/{locale}` (en/ms/zh) | Sets session locale, open-redirect guarded | — | Customer |
| Login / Register | `/login` → [UserLogin.php](../app/Livewire/Auth/UserLogin.php) | Combined sign-in/register w/ email-OTP verification & 2FA | `users` | Customer |
| Forgot password | `/forgot-password` → [ForgotPassword.php](../app/Livewire/Auth/ForgotPassword.php) | OTP-based password reset | `users` | Customer |
| Social login | `/auth/{google\|microsoft}/redirect`+`/callback` → [SocialAuthController.php](../app/Http/Controllers/SocialAuthController.php) | OAuth sign-in (provider auto-enabled when keys set) | `users`, `social_accounts` | Customer |
| Logout | `POST /logout` | CSRF-protected logout (web guard) | `sessions` | Customer |
| AI Chat widget | [Chatbot.php](../app/Livewire/Chatbot.php) (global, injected in layout) | Trilingual knowledge-base assistant w/ navigation CTAs | `chatbot_faqs`, `chat_logs` | Customer |
| Sitemap | `/sitemap.xml` | Serves generated XML sitemap | — | Public/Crawlers |

## 1.2 Admin features (Filament panel)

Mounted at `/admin` via [AdminPanelProvider.php](../app/Providers/Filament/AdminPanelProvider.php). See **Section 25** for full detail. Summary:

| Resource / Page | File | Purpose |
|---|---|---|
| Dashboard | [Dashboard.php](../app/Filament/Pages/Dashboard.php) | Stats + charts + recent activity/orders |
| Products | [ProductResource.php](../app/Filament/Resources/Products/ProductResource.php) | Full product CRUD, media, import/export |
| Categories | [CategoryResource.php](../app/Filament/Resources/Categories/CategoryResource.php) | Category CRUD + sort order |
| Brands | [BrandResource.php](../app/Filament/Resources/Brands/BrandResource.php) | Brand marquee logos CRUD |
| Orders | [OrderResource.php](../app/Filament/Resources/Orders/OrderResource.php) | Order lifecycle management + export |
| Bookings | [BookingResource.php](../app/Filament/Resources/Bookings/BookingResource.php) | Appointment management |
| Testimonials (Feedback) | [FeedbackResource.php](../app/Filament/Resources/Feedback/FeedbackResource.php) | Customer testimonials CRUD |
| Contacts | [ContactResource.php](../app/Filament/Resources/Contacts/ContactResource.php) | Contact-form submissions inbox |
| Customers | [CustomerResource.php](../app/Filament/Resources/Customers/CustomerResource.php) | Customer (client) directory |
| Users | [UserResource.php](../app/Filament/Resources/Users/UserResource.php) | Admin/staff account management |
| FAQ Page | [FaqResource.php](../app/Filament/Resources/Faqs/FaqResource.php) | Public FAQ content |
| Chatbot FAQs | [ChatbotFaqResource.php](../app/Filament/Resources/ChatbotFaqs/ChatbotFaqResource.php) | Chatbot knowledge base |
| Activity Log | [ActivityResource.php](../app/Filament/Resources/Activities/ActivityResource.php) | Who-did-what audit trail |
| Logs | [LogResource.php](../app/Filament/Resources/Logs/LogResource.php) | Application error log viewer |
| Settings | [SettingResource.php](../app/Filament/Resources/Settings/SettingResource.php) | Key-value site configuration (Shop Mode etc.) |
| System Status | [SystemStatus.php](../app/Filament/Pages/SystemStatus.php) | Health dashboard (DB, cron, disk, email) |
| Edit Profile | [Pages/Auth/EditProfile.php](../app/Filament/Pages/Auth/EditProfile.php) | Admin profile + TOTP 2FA setup |

## 1.3 Integrations

| Integration | Where | Notes |
|---|---|---|
| WhatsApp deep links | layout + components + configurator | `wa.me` links w/ pre-filled context messages |
| AI Chatbot | [Chatbot.php](../app/Livewire/Chatbot.php) + [MockDriver.php](../app/Services/Chat/MockDriver.php) | Keyword knowledge base (not a live LLM) |
| 3D Configurator | [configurator.js](../resources/js/configurator.js) | Three.js in-browser car builder → WhatsApp enquiry |
| Email (Gmail API) | [GmailApiTransport.php](../app/Mail/Transport/GmailApiTransport.php) | Sends mail as the store's Gmail via OAuth refresh token |
| Social OAuth | [SocialAuthController.php](../app/Http/Controllers/SocialAuthController.php) | Google + Microsoft (Laravel Socialite) |
| Google Maps | layout + contact page | `cid`-based deep link to the store's Maps listing |
| Payment gateway | [PaymentPage.php](../app/Livewire/PaymentPage.php) | **Mock/simulated** — no real gateway integrated |

---

# 2. UI/UX Design Details

## 2.1 Color system — "Ember Carbon" palette

The brand palette is defined as RGB CSS custom properties in [resources/views/layouts/app.blade.php](../resources/views/layouts/app.blade.php) (they flip values under `.dark`) and surfaced as Tailwind theme tokens in [resources/css/app.css](../resources/css/app.css). The Filament admin re-declares the same ramp in [AdminPanelProvider.php](../app/Providers/Filament/AdminPanelProvider.php).

Official design-system palette (exact roles):

**Brand colors**
| Token | Hex | Role |
|---|---|---|
| **Ember Red** | `#C8413D` | Primary brand · CTA · Focus ring · Navbar active |
| **Ember Dark** | `#A83432` | Hover state · Pressed button |
| **Ember Light** | `#E86460` | Active glow · Accent highlight |
| **Carbon Black** | `#121212` | Page background · Dark body |
| **Asphalt** | `#1C1917` | Card surface · Component bg |

**Surface & text scale**
| Token | Hex | Role |
|---|---|---|
| **Bone White** | `#E8E0D8` | Primary text · Off-white headings |
| **Warm Ash** | `#8C8480` | Secondary text · Muted labels |
| **Gunmetal** | `#3A3330` | Border · Divider lines |
| **Deep Slate** | `#2E2A28` | Card border · Subtle stroke |
| **Chalk** | `#F7F5F3` | Light-mode background |

> Mobile browser chrome (`theme-color`): `#C8413D` (light) / `#0C0C0E` (dark).

**Filament admin primary/danger ramp** (anchored so 600 = `#C8413D`): `50 #fdf3f2 · 100 #fbe4e3 · 200 #f7cdcb · 300 #efa9a5 · 400 #e47b76 · 500 #d6534d · 600 #c8413d · 700 #a4302d · 800 #882a28 · 900 #722827 · 950 #3e110f`. Info=Sky, Success=Emerald, Warning=Amber, Gray=Zinc.

**3D configurator paint/rim/brake hexes** (from [configurator.js](../resources/js/configurator.js)): Ember Red `0xc8413d`, Racing Yellow `0xfacc15`, Apex Blue `0x2563eb`, Asphalt Grey `0x4b5563`, Carbon Black `0x0f172a`, Liquid Silver `0xcbd5e1`, Chalk White `0xf8fafc`, Saturn Bronze `0xa87c43`, Imperial Gold `0x8a7345`.

## 2.2 Typography

Google Fonts loaded with `preconnect` + `display=swap` in the layout head. Two-font system:

| Role | Font | Treatment |
|---|---|---|
| **Display / Heading** | **Anton** | Heavy condensed, max impact — **forced All-Caps & locked at 400 weight** (uppercase removed for non-English locales) |
| **Body** | **DM Sans** (400 / 500 / 600 / 700) | Modern, neutral, great at small sizes — applied globally to all interface components |

- `--font-display: 'Anton', ui-sans-serif, system-ui, sans-serif`
- `--font-sans: 'DM Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'`

## 2.3 Layout patterns
- Tailwind utility grids (`grid grid-cols-*`, responsive `md:`/`lg:` column counts) throughout views.
- Centered max-width containers (`max-w-7xl mx-auto px-*`).
- Tailwind breakpoints: `sm` 640 / `md` 768 / `lg` 1024 / `xl` 1280 / `2xl` 1536.
- `overflow-x: clip` on `html,body` (with `hidden` fallback) in [app.css](../resources/css/app.css) to prevent AOS entrance offsets from causing horizontal scroll on phones.

## 2.3b Design reference libraries

The visual language is sourced from a fixed set of reference libraries (kept consistent site-wide):

| Category | Library | Used for |
|---|---|---|
| **Icons** | [lucide.dev](https://lucide.dev) | Lucide SVG icon set — UI icons, per-category icons on the home page |
| **Brand logos** | [svgl.app](https://svgl.app) | Brand SVG logos (WhatsApp, Facebook, payment-method marks) |
| **Hover FX** | [itshover.com](https://itshover.com) | CSS hover-interaction patterns for buttons/cards |
| **Payment marks** | from **svgl.app** | Checkout payment logos: `visa.svg` + `mastercard.svg` (committed to `public/images/payment/`); GrabPay / ShopeePay / Boost referenced as `images/payment/*.svg`. **Touch 'n Go** has no open-licensed logo, so it's rendered as a **styled text mark in its brand blue** instead. |

> Filament admin uses **Heroicons** (its native icon set); the storefront uses **Lucide**.

## 2.4 Component library (reusable UI)
| Component | File |
|---|---|
| WhatsApp button | [components/btn/whatsapp.blade.php](../resources/views/components/btn/whatsapp.blade.php) |
| Facebook button | [components/btn/facebook.blade.php](../resources/views/components/btn/facebook.blade.php) |
| WhatsApp / Facebook icons | [components/icon/](../resources/views/components/icon/) |
| Tooltip (`<x-tooltip>`) | [components/tooltip.blade.php](../resources/views/components/tooltip.blade.php) — Alpine-driven, **4 positions** (top/bottom/left/right), shows on **hover AND keyboard focus** (`focusin`/`focusout` — a11y), scale+fade transition. Used across cart, product detail, products, booking, chatbot. Filament admin uses native `->tooltip()` extensively (product stock/sale/toggle columns, actions). |
| Page loader | [components/page-loader.blade.php](../resources/views/components/page-loader.blade.php) |
| Mail layout | [components/mail/layout.blade.php](../resources/views/components/mail/layout.blade.php) |
| Global confirm modal | Alpine `$store.confirm` in layout (`x-show="$store.confirm.show"`) |
| Cart drawer | Alpine `x-data="{ cartOpen }"` slide-in panel in layout nav |

## 2.5 Animations & transitions
- **AOS (Animate On Scroll) 2.3.4** — CDN-loaded; scroll-triggered fade/slide entrances across pages.
- **Brand marquee** — JS-driven infinite scroll in [home-page.blade.php](../resources/views/livewire/home-page.blade.php) (`BASE_SPEED` constant, tuned to `0.18`).
- **3D camera transitions** — `easeInOutCubic` easing for view changes in [configurator.js](../resources/js/configurator.js).
- **Filament SPA navigation** — top progress bar + skeleton overlay ([nav-loading.blade.php](../resources/views/filament/nav-loading.blade.php)) on `livewire:navigate`.
- Tailwind `transition-*`/`duration-*`/hover utilities throughout.
- (See Section 17 for the full per-interaction breakdown.)

## 2.6 Loading states
- **Filament skeleton loaders** — per-page-type variants (list/form/dashboard/status) in [nav-loading.blade.php](../resources/views/filament/nav-loading.blade.php).
- **3D loader** — real download-progress bar 0→100% (`#loader-progress-bar`, `#loader-percentage`) streaming the `.glb` in [configurator.js](../resources/js/configurator.js).
- **Configurator lazy-load busy state** — button `aria-busy` while bundle downloads ([configurator-loader.js](../resources/js/configurator-loader.js)).
- Livewire `wire:loading` indicators in interactive components.
- Page loader component ([page-loader.blade.php](../resources/views/components/page-loader.blade.php)).

## 2.7 Empty & error states
- Custom error pages: `404`, `419` (CSRF/expired), `429` (rate-limited), `500`, `503` (maintenance), plus `unauthorized` — all in [resources/views/errors/](../resources/views/errors/).
- Filament resources rely on default empty-state placeholders; tables use `->placeholder('—')` for null cells.
- Cart/catalog empty states rendered conditionally in their Blade views.
- Exception rendering customized in [bootstrap/app.php](../bootstrap/app.php) (auth-failure on `admin*` redirects to `/unauthorized`).

## 2.8 Navigation patterns
- Sticky top nav with Alpine state (`cartOpen`, language dropdown `open`).
- **Shop-Mode-aware nav** — cart icon/links only render when `ONLINE_SHOPPING_ENABLED === 'true'` ([layouts/app.blade.php:552](../resources/views/layouts/app.blade.php)).
- Mobile menu (Alpine toggle), language selector dropdown, theme toggle.
- Admin: collapsible sidebar (`sidebarFullyCollapsibleOnDesktop()`) with grouped navigation.

---

# 3. Non-Functional Requirements (NFR)

## 3.1 Performance
| Technique | Evidence |
|---|---|
| Image conversions | [Product.php](../app/Models/Product.php) `registerMediaConversions()` → `thumb` (400×300, sharpen+optimize), `card` (800×600, optimize); also on [Service.php](../app/Models/Service.php) |
| Image optimizers installed | [Dockerfile](../Dockerfile) installs `jpegoptim optipng pngquant gifsicle webp` for Spatie MediaLibrary |
| Asset bundling | Vite 8 + `@tailwindcss/vite` + `laravel-vite-plugin` ([vite.config.js](../vite.config.js)) |
| 3D bundle lazy-load | Three.js (~640 KB) dynamic-imported only on configurator open ([configurator-loader.js](../resources/js/configurator-loader.js)) |
| DRACO mesh compression | `car-draco.glb` (24.5 MB) / `city-draco.glb` (1.9 MB) compressed models used in prod |
| DB indexing | Dedicated index migrations: [add_performance_indexes_to_core_tables](../database/migrations/2026_04_29_174552_add_performance_indexes_to_core_tables.php), [add_performance_indexes_to_orders_table](../database/migrations/2026_06_20_113634_add_performance_indexes_to_orders_table.php), [add_index_to_app_logs_user_id](../database/migrations/2026_06_21_000002_add_index_to_app_logs_user_id.php) |
| Settings caching | [Setting::getValue()](../app/Models/Setting.php) caches each key for 1 hour |
| Cache/route/view caching | `config:cache`, `route:cache`, `view:cache` run on container boot ([docker-entrypoint.sh](../docker-entrypoint.sh)) |
| OPcache tuning | Documented in global php.ini (dev) — not in repo |
| N+1 prevention | Eager loading in Livewire queries (e.g. ProductsPage with category) |

## 3.2 Accessibility (a11y)
| Aspect | Evidence |
|---|---|
| Semantic HTML | `<nav>`, `<main id="main-content">`, headings hierarchy in layout |
| ARIA | `aria-busy` on configurator trigger; ARIA labels on icon buttons |
| Alt text | Product/brand images render with alt attributes in views |
| Focus / `sr-only` | Tailwind `sr-only` utilities + focus utilities in views |
| Color contrast | Light-mode contrast fixes applied (e.g. hero location text `text-gray-600`) |
| Reduced layout shift | Font `display=swap`, fixed image conversion dimensions |

> Coverage is partial/best-effort, not formally WCAG-audited. Marked here as implemented where evidence exists.

## 3.3 Responsiveness

**Mobile-first** throughout — base styles target mobile, responsive prefixes scale up. Verified utility usage across [resources/views/](../resources/views/): **~190 `sm:`**, **67 `md:`**, **74 `lg:`**, **3 `xl:`** responsive class occurrences.

| Aspect | Evidence |
|---|---|
| Breakpoints | Tailwind `sm` 640 / `md` 768 / `lg` 1024 / `xl` 1280 (mobile-first base) |
| Responsive grids | `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3/4` patterns (product/category/testimonial grids) — single column on phones, multi-column on larger screens |
| Show/hide by device | 17× `hidden md:block` + 8× `md:hidden` — e.g. desktop nav vs mobile **hamburger menu**, columns dropped on phones |
| Fluid type/spacing | `text-3xl sm:text-4xl`, `py-16 sm:py-20` scale headings/spacing per breakpoint |
| Viewport | `<meta name="viewport" content="width=device-width, initial-scale=1.0">` |
| Horizontal-scroll guard | `overflow-x: clip` (→ `hidden` fallback) on html/body in [app.css](../resources/css/app.css) so AOS offsets don't cause sideways scroll on phones |
| Touch targets / gestures | Configurator pinch-zoom + drag (OrbitControls); `touchstart` listener so iOS Safari renders `:active` tap feedback |
| Admin responsiveness | [resources/css/admin.css](../resources/css/admin.css) (211 lines) — explicit `@media (max-width: 1024/768/640px)` rules: sidebar becomes an **overlay**, charts/tables stack, logo shrinks (`max-width: 140px`), forms stack, page-header titles clamp to prevent overflow; tested down to **375px** |
| Admin dashboard grid | Responsive widget columns: default 1 / sm 2 / md 4 / xl 12 ([Dashboard.php](../app/Filament/Pages/Dashboard.php)) |
| Filament tables | `->visibleFrom('md')` + `toggleable()` drop non-essential columns on small screens; collapsible sidebar |

> Responsive design was specifically audited across breakpoints during development. **No** `srcset`/`sizes` responsive-image markup (fixed-size conversions are used instead).

## 3.4 SEO
(See Section 21 for full detail.) Implemented via `artesaos/seotools`: `SEOMeta`, `OpenGraph`, `JsonLd` generated in [layouts/app.blade.php](../resources/views/layouts/app.blade.php); `robots.txt`, dynamic `sitemap.xml`, structured data.

## 3.5 Internationalization (i18n)
- **3 languages:** English (default), Bahasa Melayu (`ms`), Chinese (`zh`).
- Translation files: [lang/ms.json](../lang/ms.json), [lang/zh.json](../lang/zh.json) (EN is the source key).
- **Translatable model fields:** `Product.description`, `description_ms`, `description_zh` → resolved by [`getTranslatedDescriptionAttribute()`](../app/Models/Product.php) per locale.
- Locale switcher: `/lang/{locale}` route → session, applied by [SetLocale.php](../app/Http/Middleware/SetLocale.php) middleware.
- Chatbot detects and replies in the language the user typed ([Chatbot.php](../app/Livewire/Chatbot.php)).
- Currency formatted as MYR (`->money('MYR', locale: 'ms_MY')`).

## 3.6 Maintainability
- **Activity logging** — Spatie ActivityLog `LogsActivity` trait on User, Product, Category, Order, Booking, Feedback, Contact, Setting, Service (password excluded from logs).
- **Modularity** — Livewire components per page; Filament resources split into `Schemas/`, `Tables/`, `Pages/`; services under `app/Services/`.
- **Config management** — `.env` driven; documented in [.env.example](../.env.example).
- **Logging** — multi-channel stack (`single`, structured JSON, DB) in [config/logging.php](../config/logging.php) with a custom DB log handler ([app/Logging/](../app/Logging/)).

## 3.7 Reliability
| Aspect | Evidence |
|---|---|
| Validation | Livewire `$rules`/`validate()` in every form component; Filament form rules |
| Error handling | `try/catch` around chatbot AI call + OTP send; custom exception render in [bootstrap/app.php](../bootstrap/app.php) |
| Graceful fallbacks | [`Product::getImageUrl()`](../app/Models/Product.php) (media → legacy storage), chatbot connection-failure fallback to WhatsApp, brand text fallback when no logo |
| DB transactions | `Order::generateOrderNumber()`, `Booking::generateReference()`, `CartItem::claimGuestCart()`, locked add-to-cart all use `DB::transaction` + `lockForUpdate` |
| Race-safety | Double-booking + order-number concurrency guards (see recent commits) |
| Self-healing data growth | Prunable models + scheduled prune/trim commands |

---

# 4. Security Implementation

| # | Control | Evidence |
|---|---|---|
| 4.1 | Authentication | Multi-guard: `web` (customers) + `admin` (Filament). Email/password, email-OTP 2FA, social OAuth. Admin TOTP app-auth via Filament `multiFactorAuthentication()` |
| 4.1b | Brute-force / login lockout | **Apple-style progressive lockout** per email+IP ([UserLogin.php](../app/Livewire/Auth/UserLogin.php)): 5 fails→30 s, 10→60 s, 15→5 min, 20→15 min, 25+→1 hr; plus a hard **IP block after 30 attempts** (1 hr) for bot/spray detection. *Fixed 2026-07-02:* lockouts now fire only at tier boundaries (every 5th failure) with the "N attempts remaining" countdown between tiers, and every auth action resets Livewire's persisted error bag so retries show the **current** message instead of the first stale one (regression-guarded by [LoginLockoutTest](../tests/Feature/LoginLockoutTest.php)) |
| 4.2 | Authorization | Role-based (`owner`/`admin`/`staff`/`client`) via [User.php](../app/Models/User.php) `isAdmin()`/`isStaff()`/`canAccessPanel()`; 12 Laravel Policies in [app/Policies/](../app/Policies/); `AdminMiddleware` |
| 4.3 | CSRF | Laravel `PreventRequestForgery` middleware; `@csrf`/`<meta csrf-token>`; logout is POST-only |
| 4.4 | XSS | Blade `{{ }}` auto-escaping default; `{!! !!}` used only for trusted SEO tag generators; CSP `object-src 'none'` |
| 4.5 | SQL injection | Eloquent ORM + parameter binding throughout; no raw user-interpolated SQL |
| 4.6 | Password hashing | `'password' => 'hashed'` cast (bcrypt) in [User.php](../app/Models/User.php); `BCRYPT_ROUNDS=12` |
| 4.6b | Password strength policy | **Centralized** `Password::defaults()` in [AppServiceProvider.php](../app/Providers/AppServiceProvider.php): min 8 chars + mixed case + numbers + symbols + **`->uncompromised()`** (HaveIBeenPwned k-anonymity breach check). Applied everywhere — registration, password reset, profile change, set-password, admin user create. Breach check skipped only under `testing`. |
| 4.7 | Input validation | Livewire component rules + Filament form rules; settings validated per-key (regex/integer/date_format) in [SettingResource.php](../app/Filament/Resources/Settings/SettingResource.php) |
| 4.8 | File upload security | Product media `acceptsMimeTypes(['image/jpeg','png','webp','gif'])` + `singleFile()`; Filament `FileUpload->image()` |
| 4.9 | Session security | `http_only=true`, `same_site=lax`, `encrypt` configurable, DB-driven sessions, `secure` cookie via env ([config/session.php](../config/session.php)); `AuthenticateSession` middleware |
| 4.10 | Rate limiting | `throttle:5,1` (invoices, order tracker), `throttle:20,1` (payment); OTP resend 60 s cooldown; register 5/10 min per IP ([UserLogin.php](../app/Livewire/Auth/UserLogin.php), [EmailOtpService.php](../app/Services/EmailOtpService.php)) |
| 4.11 | HTTPS/SSL readiness | `trustProxies()` in [bootstrap/app.php](../bootstrap/app.php) so URLs render `https://` behind Render's proxy; HSTS header when `request->secure()`. *Hardened 2026-07-02:* proxy list is env-configurable (`TRUSTED_PROXIES`, default `*` for Render) and **X-Forwarded-Host is no longer trusted** — only For/Proto/Port — so a spoofed forwarded host can't poison generated URLs (reset links, signed URLs) |
| 4.11b | Lookup anti-enumeration | Order & booking trackers return **one unified message** for both "not found" and "wrong email" ([OrderTracker.php](../app/Livewire/OrderTracker.php), [BookingTracker.php](../app/Livewire/BookingTracker.php)) — sequential `ORD-/BK-` numbers can't be probed for existence (business-volume leak); invoice route additionally throttled `5,1` |
| 4.12 | Sensitive data | `.env`, `.env.*` git-ignored; password excluded from activity log & `#[Hidden]`; `role` **not** mass-assignable (privilege-escalation guard); seeder refuses to run without explicit admin creds (no public default) |
| 4.13 | Secret-leak audit (verified) | **Whole git history scanned — no secrets leaked.** Only `.env.example` (placeholder template) is tracked; the real `.env` was never committed; TiDB host/username/password appear in **no** commit; the one-off credential scripts used during deploy were never committed; no hardcoded passwords/API keys/tokens in any tracked file; this audit doc itself contains no live credentials. Live secrets exist only in the local `.env` + Render's encrypted env vars. |
| 4.14 | Production DB protection | TiDB Serverless is **TLS-enforced + username/password** (plain connections are rejected). **Not** a "public, unprotected" database. Caveat: the free tier has **no IP allowlist** by default, so the (strong, random, un-leaked) credential is the sole gatekeeper — optional hardening: restrict to Render's egress IPs in the TiDB console. |
| — | Security headers | [SecurityHeaders.php](../app/Http/Middleware/SecurityHeaders.php): CSP, X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy, HSTS; strips `X-Powered-By`/`Server` |
| — | Honeypot | `spatie/laravel-honeypot` enabled w/ randomized field name + timing check ([config/honeypot.php](../config/honeypot.php)) |
| — | Open-redirect guard | Language switcher + social callback only return to same-host URLs |
| — | Cron endpoint | `/cron/run-schedule/{token}` gated by `hash_equals` against `CRON_SECRET` |

---

# 5. Database Schema

Source: [database/migrations/](../database/migrations/) (67 migration files). Production DB = **TiDB Serverless (MySQL-compatible)**; local = SQLite.

## Tables

| Table | Key columns | Notes |
|---|---|---|
| `users` | name, email (unique), email_verified_at, password (nullable), role, phone, gender, address_line, city, postcode, state, two_factor_enabled, app_authentication_secret, app_authentication_recovery_codes, deleted_at | **SoftDeletes**; role default `client` |
| `password_reset_tokens` | email (pk), token, created_at | |
| `sessions` | id (pk), user_id, ip_address, user_agent, payload, last_activity (idx) | DB session driver |
| `cache` / `cache_locks` | key, value, expiration | DB cache store |
| `jobs` / `job_batches` / `failed_jobs` | queue payloads | `QUEUE_CONNECTION=database`, but no `queue:work` process runs anywhere — should normally stay empty (see 9 / 31.8) |
| `categories` | name, slug, description, image, is_active, sort_order | |
| `products` | category_id (FK nullOnDelete), name, slug (unique), brand, description(+_ms/_zh), short_description, price, sale_price, sku (unique), stock, image, images(json), specs(json), compatible_vehicles(json), model_url, has_3d, is_active, is_featured | |
| `contacts` | name, email, phone, subject, message, is_read | **SoftDeletes** |
| `feedback` | customer name, rating, content, image, source, is_featured, sort_order, deleted_at | **SoftDeletes** (testimonials) |
| `orders` | order_number, user_id (FK), status, total_amount, subtotal, shipping_*, payment_method, payment status, cancellation fields, lifecycle timestamps (paid/shipped/delivered/cancelled), deleted_at | **SoftDeletes** |
| `order_items` | order_id (FK), product_id (FK), product_name, quantity, price | |
| `services` | name, description, price, duration, image, is_active, sort_order, scheduling fields | media-enabled |
| `bookings` | reference, customer_name/phone/email, service_id (FK), user_id (FK), preferred_date, preferred_time, notes, status, reminder_sent_at, deleted_at | **SoftDeletes** |
| `car_models` | brand, model, year_from, year_to | Seeded via CarModelSeeder; **no Filament admin UI** |
| `product_compatibilities` | product_id (FK), car_model_id (FK) | pivot (many-to-many); **no admin UI** — product-level fitment is instead captured in the product's `compatible_vehicles` JSON field (KeyValue) |
| `media` | Spatie MediaLibrary polymorphic table | conversions stored |
| `cart_items` | user_id, session_id (nullable), product_id (FK), quantity | **Prunable** (30 d) |
| `settings` | key (string pk), value | key-value config |
| `chat_logs` | session, message, reply, lang, meta | **Prunable** (90 d) |
| `chatbot_faqs` | question, answer, keywords, priority, is_active, lang fields | |
| `faqs` | question, answer (+translations), sort_order, is_published | |
| `brands` | name, logo, display_type (enum text/image), website_url, sort_order, is_active | |
| `social_accounts` | user_id (FK cascade), provider, provider_id, provider_email, avatar | |
| `app_logs` | level_name, message, context(json), trace_id, user_id (idx), resolved_at | **Prunable** (LOG_DB_RETENTION_DAYS) |
| `activity_log` | log_name, description, subject (morph, **subject_id widened to string**), causer (morph), event, ip_address, attribute_changes(json), properties(json) | Spatie ActivityLog |
| `notifications` | Laravel DB notifications (polymorphic) | Filament bell icon |
| `imports` / `exports` / `failed_import_rows` | Filament import/export job tracking | |

**Foreign keys:** products→categories, orders→users, order_items→orders/products, bookings→services/users, product_compatibilities→products/car_models, social_accounts→users, cart_items→users/products.

**Soft deletes:** `users`, `orders`, `bookings`, `feedback`, `contacts`.
**Prunable (auto-cleanup):** `cart_items`, `chat_logs`, `app_logs`.

---

# 6. Technology Stack

## 6.1 Backend (PHP — [composer.json](../composer.json))
| Package | Version | Purpose |
|---|---|---|
| php | ^8.3 (runs 8.4 in Docker) | Language runtime |
| laravel/framework | ^13.0 | Web framework |
| livewire/livewire | ^4.2 | Reactive server-rendered components |
| filament/filament | ^5.5 | Admin panel |
| filament/spatie-laravel-media-library-plugin | ^5.5 | Media uploads in Filament |
| laravel/socialite | ^5.28 | OAuth social login |
| socialiteproviders/microsoft | ^4.9 | Microsoft OAuth provider |
| spatie/laravel-medialibrary | ^11.21 | Image management + conversions |
| spatie/laravel-activitylog | ^5.0 | Audit logging |
| spatie/laravel-honeypot | ^4.7 | Spam protection |
| spatie/laravel-sitemap | ^8.1 | Sitemap generation |
| barryvdh/laravel-dompdf | ^3.1 | PDF invoice generation |
| artesaos/seotools | ^1.4 | SEO meta / OG / JSON-LD |
| intervention/image-laravel | ^4.0 | Installed but **unused** — no `Intervention\Image` reference anywhere in `app/`; Spatie Media Library's actual conversions run on the `gd` driver (`config/media-library.php`), not this package |
| laravel/tinker | ^3.0 | REPL |

## 6.2 Frontend (JS — [package.json](../package.json))
| Package | Version | Purpose |
|---|---|---|
| three | ^0.184.0 | 3D rendering engine |
| tailwindcss | ^4.0.0 | Utility CSS |
| @tailwindcss/vite | ^4.0.0 | Tailwind Vite plugin |
| vite | ^8.0.0 | Build/bundler |
| laravel-vite-plugin | ^3.0.0 | Laravel asset integration |
| @gltf-transform/cli | ^4.4.0 | GLB/DRACO model optimization |
| axios | ^1.15.0 | HTTP client |
| concurrently | ^9.0.1 | Run dev processes together |

External CDN libs (loaded in layout, not npm): **AOS 2.3.4**, **Leaflet 1.9.4** (map).

## 6.3 Dev tools
| Package | Purpose |
|---|---|
| laravel/pint | Code style |
| laravel/pail | Log tailing |
| phpunit/phpunit ^12 | Testing |
| mockery/mockery | Test mocks |
| nunomaduro/collision | CLI error rendering |
| fakerphp/faker | Test data |
| barryvdh/laravel-debugbar | Debug toolbar (off by default) |

---

# 7. Third-party Integrations

- **WhatsApp** — `wa.me/<phone>` deep links throughout; phone from `config('services.store.phone_raw')` (`60169150917`). Configurator builds a full itemized enquiry message (Section 10.7).
- **Chatbot (rule-based)** — Bound service is [MockDriver.php](../app/Services/Chat/MockDriver.php) (singleton in [ChatServiceProvider.php](../app/Providers/ChatServiceProvider.php)) implementing [ChatServiceInterface](../app/Contracts/ChatServiceInterface.php). It is a **rule-based (retrieval-based) chatbot** using a **priority-ranked keyword knowledge base** with Levenshtein-distance typo tolerance, *not* a live LLM call — admin-managed `chatbot_faqs` are the source of truth, with built-in trilingual knowledge as fallback/seed. Trilingual matching (Traditional→Simplified, typo tolerance), navigation-intent CTAs, page-context awareness, connection-failure fallback to WhatsApp.
- **3D Viewer** — Custom Three.js implementation (GLTFLoader + DRACOLoader + OrbitControls). **No** model-viewer / Tripo / Babylon.
- **Payment gateway** — **Simulated, no real gateway.** The checkout/payment flow mimics Malaysian methods: **FPX** (with a participating-bank picker — Maybank2u, CIMB Clicks, Public Bank PBe, RHB Now, Hong Leong Connect…), **e-wallet** (Touch 'n Go eWallet etc.), and **card** ([CheckoutPage.php](../app/Livewire/CheckoutPage.php), [PaymentPage.php](../app/Livewire/PaymentPage.php)). No funds move; the order is marked paid on confirmation.
- **Email** — Custom **Gmail API transport** ([GmailApiTransport.php](../app/Mail/Transport/GmailApiTransport.php)) sending as the store Gmail via an OAuth refresh token obtained through `/gmail-send/connect`. SMTP config also present as fallback.
- **Maps** — Google Maps `cid` deep link + Leaflet interactive map on contact page.

---

# 8. Project File Structure (depth ≈3)

```
capstone/
├── app/
│   ├── Console/Commands/       # 5 scheduled commands (sitemap, expire-orders, reminders, log-trim, auto-resolve)
│   ├── Contracts/              # ChatServiceInterface
│   ├── Exceptions/             # OtpSendFailedException
│   ├── Filament/               # Admin panel: Resources/, Pages/, Widgets/, Exports/, Imports/, Concerns/
│   ├── Http/
│   │   ├── Controllers/        # Invoice, SocialAuth, GmailSendSetup
│   │   └── Middleware/         # 6 custom middleware (SecurityHeaders, ShoppingEnabled, SetLocale, …)
│   ├── Livewire/               # Storefront page components + Auth/ + Chatbot
│   ├── Logging/                # Custom DB log channel
│   ├── Mail/                   # 10 mailables + Transport/GmailApiTransport
│   ├── Models/                 # 20 Eloquent models + Concerns/ (HasSortableOrder trait)
│   ├── Notifications/          # EmailOtp
│   ├── Policies/               # 12 authorization policies
│   ├── Providers/              # AppServiceProvider, ChatServiceProvider, Filament/AdminPanelProvider
│   └── Services/               # Chat/, Booking/, EmailOtpService, ShippingCalculator, RefundCalculator
├── bootstrap/                  # app.php (middleware, proxies, exceptions)
├── config/                     # 16 config files
├── database/
│   ├── migrations/             # 67 migrations
│   └── seeders/                # DatabaseSeeder + Car/Faq/Feedback/Product/ChatbotFaq seeders
├── docs/                       # SYSTEM_AUDIT.md (this file)
├── lang/                       # ms.json, zh.json
├── public/                     # entry, compiled assets, models/3d/, favicons, robots.txt
├── resources/
│   ├── css/                    # app.css, admin.css, configurator.css
│   ├── js/                     # configurator.js, configurator-loader.js, app.js
│   └── views/                  # layouts/, livewire/, components/, filament/, partials/, errors/, invoice
├── routes/                     # web.php, console.php
├── storage/app/public/         # committed product + brand media (Render has no persistent disk)
├── Dockerfile                  # 3-stage build (vendor → assets → runtime)
├── docker-entrypoint.sh        # storage:link, migrate, cache, serve
└── .env.example                # documented env template
```

---

# 9. Known Gaps / TODO

- **No `// TODO` / `// FIXME` / `// HACK` comments** found in `app/`, `resources/`, `config/`, `routes/` (clean scan).
- **Payment gateway** — hybrid: **Stripe Checkout in TEST mode** handles card / FPX / GrabPay behind the `PAYMENT_MODE` setting (default `demo` = fully simulated). Session creation + method mapping in [StripeCheckoutService](../app/Services/Payments/StripeCheckoutService.php); the idempotent settle step shared by all confirmation paths in [OrderPaymentService](../app/Services/Payments/OrderPaymentService.php); signature-verified webhook at `POST /stripe/webhook` ([StripeWebhookController](../app/Http/Controllers/StripeWebhookController.php), CSRF-exempt in `bootstrap/app.php`) plus a success-URL re-verification in [PaymentPage.php](../app/Livewire/PaymentPage.php). Touch 'n Go / ShopeePay / Boost stay simulated (unsupported by Stripe MY). Live keys are refused by design (`sk_test_` only) — production onboarding still outstanding.
- **Chatbot** — rule-based (retrieval-based) knowledge-base matcher, not a live LLM.
- **Microsoft OAuth** — code-complete but only enabled when keys are set; production uses Google only.
- **File-storage persistence** — Render free tier has no persistent disk; admin-uploaded media after deploy must be committed to git to survive redeploys (mitigated by tracking `storage/app/public`).
- **Queue** — `QUEUE_CONNECTION=database` in production, but no `queue:work` process runs anywhere (Render only runs `php artisan serve`). The one `ShouldQueue` job in the app — Spatie Media Library's conversion job for product thumb/card images — was queued by default and would have sat in the `jobs` table forever, leaving uploaded images broken. Fixed by forcing `queue_conversions_by_default=false` (see 25.14), so conversions run synchronously instead; nothing in the app relies on a real queue worker.
- **Accessibility** — best-effort, not formally WCAG-audited.
- **PWA** — `site.webmanifest` present but **no service worker** (not an installable offline PWA).
- **Dormant interface method** — [ChatServiceInterface](../app/Contracts/ChatServiceInterface.php) declares `recommend()` (product recommendation) but it is **not wired up anywhere** (only `chat()` and `generateDescription()` are used).
- **Removed feature** — a `gallery_items` table/feature existed earlier and was **fully dropped** (migration `drop_gallery_items_table`); no gallery in the live app.
- **Backup hardening** — no app-level automated DB backup (relies on TiDB's managed backups); adding a scheduled `mysqldump`/`spatie/laravel-backup` export would give an independent off-provider copy (see §22.8).
- **DB network hardening** — TiDB free tier has no IP allowlist; restricting connections to Render's egress IPs would add defense-in-depth beyond the credential (see §4.14). *(No secret leak found — see §4.13.)*
- **Sequential order/booking numbers** — `ORD-YYYY-NNNNN` / `BK-YYYY-NNNNN` are guessable by design (customers quote them verbally). Accepted risk: lookups require the matching email and return a unified not-found message (§4.11b); a random suffix would close the residual volume-inference channel if ever needed.
- **CSP `unsafe-inline`/`unsafe-eval`** — required by the page's inline scripts and Livewire/Alpine; high-value directives (`object-src 'none'`, `frame-ancestors`, `base-uri`, `form-action`) are enforced. Migrating to nonces is a possible future hardening.
- **Transactional emails are English-only by design** — every Mailable pins `locale('en')` (matches the invoice convention); revisit only if the owner wants per-customer language emails.
- **Services are display-only by design** (owner decision, 3 Jul 2026) — no admin CRUD is wanted; service content is seeded and shown on the services page / booking wizard / chatbot. Consequently [ServicePolicy](../app/Policies/ServicePolicy.php) and the Service model's medialibrary pipeline are unused by design.
- **Known dead code, accepted as-is** (owner decision, 3 Jul 2026 — inert, no runtime impact): the CarModel/ProductCompatibility compatibility chain (+ `product_compatibilities` table, seeded by CarModelSeeder; the live feature uses `products.compatible_vehicles` JSON instead); `products.images` and `services.buffer_after` columns; `HomePage::$showcaseProduct` query (never rendered); the product-detail `#3d-mount-product` placeholder (no JS ever mounts it — the products-page configurator is separate and fully wired); helper methods `EmailOtpService::hasActiveCode()`, `Order::getNextStatusAttribute()`, `User::isClient()`, `ChatServiceInterface::recommend()`; ~336 orphaned zh/ms translation keys from removed features; duplicated error-page CSS and inline WhatsApp/service-icon SVGs.

---

# 10. Interactive 3D Configurator

**Files:** [resources/js/configurator.js](../resources/js/configurator.js) (1,579 lines), [resources/js/configurator-loader.js](../resources/js/configurator-loader.js), [resources/css/configurator.css](../resources/css/configurator.css), model assets in `public/models/3d/`.

> **Scope note:** The single working 3D feature is the **global car configurator** (opened from the hero/products promo). An earlier **per-product 3D viewer** (`products.has_3d` + `model_url`, a `#3d-mount-product` div on the product detail page) was **abandoned** — it is only a placeholder mount with fallback text and **no viewer script** ever wired to it. The DB columns still exist but the feature is dropped; treat it as not implemented.

## 10.1 3D Engine
**Three.js** (`three@^0.184.0`) with `OrbitControls`, `GLTFLoader`, `DRACOLoader`. WebGL2/WebGL/experimental-webgl capability detection before init.

## 10.2 3D Showroom / views
- View modes: **exterior** ↔ **interior** (`state.viewMode`).
- Interior position toggle: **driver** ↔ **center/console** (`state.interiorPosMode`, `#toggle-interior-pos-btn`).
- Camera reset button (`#camera-reset-btn`).

## 10.3 Loading progress bar
Real streamed download progress (0→100%) — `streamGlb()` reports bytes received vs total; `setProgress()` drives `#loader-progress-bar` width + `#loader-percentage` text (90% download → 92% parse → 100% ready).

## 10.4 Customization options (all verified in code)
**Base price:** `RM 150,000`.

| Category | Options (name · price) |
|---|---|
| **Paint colors** | Ember Red, Racing Yellow, Apex Blue, Asphalt Grey, Carbon Black, Liquid Silver, Chalk White |
| **Rim colors** | Default, Matte Black, Chalk White, Liquid Silver, Saturn Bronze, Imperial Gold |
| **Brake colors** | Ember Red, Apex Blue, Racing Yellow, Chalk White, Carbon Black |
| **Rim styles** | Sport Rims (Default, +0) · Vossen CV3 (+1,200) · BBS Super RS (+1,800) · Rotiform LAS-R (+1,500) · HRE P101 (+2,200) · Advan Racing GT (+2,000) · TE37 Black Edition (+2,500) |
| **Spoilers** | Integrated Lip (Default, +0) · Carbon Fiber High Wing (+1,200) · GT Performance Wing (+1,500) · Sleek Ducktail Wing (+600) |
| **Front bumpers** | Standard Sport (Default, +0) · Widebody Spec Bumper (+2,200) |
| **Window tint** | 100% · 70% · 50% · 35% · 15% · 5% (darkness via `TINT_MAP`) |
| **Dash cameras** | None (Default) · Mohawk · 70mai · DDPAI (all +0) |

> Note vs your brief: rim colors are 6 (no separate "Dark Gold" beyond Imperial Gold), and the configurator's paint set is 7 (no separate "White"/"Silver"/"Grey" duplicates — "Chalk White", "Liquid Silver", "Asphalt Grey" cover those).

## 10.5 Controls
- **Drag-to-rotate** + **scroll-to-zoom** + **pinch-to-zoom** via OrbitControls (`enableZoom` toggled per view/state).
- Mesh part detection via regex (`rim1-7`, `wing/spoiler 1-4`, `bumperF1-3`, `bumperB1`) for targeted recoloring.

## 10.6 Configuration summary panel
Live-updating summary elements: `#summary-rim-color`, `#summary-brake-color`, `#summary-window-tint`, `#summary-rims-price`, `#summary-spoiler-price`, `#summary-bumper-price`, total = base + rims + spoiler + bumper + dashcam (formatted `RM x,xxx` / "Included").

## 10.7 "Enquire Configuration" → WhatsApp
`#enquire-config-btn` builds a formatted message listing paint, rim style + color + price, brake color, spoiler + price, bumper + price, tint %, dash cam, base price, estimated total — then opens `https://wa.me/<storePhone>?text=<encoded>` in a new tab (phone from button `data-phone`).

## 10.8 Asset loading strategy
- Configurator JS bundle dynamic-imported on first open (keeps Products page light).
- DRACO-compressed `.glb` streamed with progress; auto-reopens on refresh if URL hash is `#car-configurator`.
- Model sizes: `car-draco.glb` 24.5 MB, `city-draco.glb` 1.9 MB (uncompressed originals 188 MB / 79 MB are git-ignored, unused by the site).

---

# 11. E-Commerce & Cart System

**Files:** [CartPage.php](../app/Livewire/CartPage.php), [CheckoutPage.php](../app/Livewire/CheckoutPage.php), [CartItem.php](../app/Models/CartItem.php), [ShippingCalculator](../app/Services/ShippingCalculator.php).

| # | Feature | Implementation |
|---|---|---|
| 11.1 | Cart drawer | Alpine slide-in panel (`x-data="{ cartOpen }"`) in layout nav |
| 11.2 | Add to cart | `CartPage::addToCart()` (static) — locked read-then-write in a `DB::transaction` |
| 11.3 | Cart persistence | DB-backed (`cart_items`); guest carts keyed by `session_id`, **claimed to user_id on login** (`CartItem::claimGuestCart()`); prunable after 30 days |
| 11.4 | Cart counter badge | Real-time count in nav, updates via Livewire events |
| 11.5 | Checkout flow | `auth` + `ShoppingEnabled` middleware — **sign-in required**; pre-fills last shipping address |
| 11.6 | Stock indicators | Stock-warning computed property; **backorders allowed** (can exceed on-hand stock, capped per-line) |
| 11.7 | Empty cart state | Conditional rendering in cart Blade view |
| 11.8 | "Added to cart" feedback | Livewire dispatch events / toast |
| 11.9 | Sign-in before checkout | Enforced by route middleware (redirect to login) |

### Payment button — 3-layer concurrency protection
[PaymentPage::pay()](../app/Livewire/PaymentPage.php) guards the most security-sensitive transition (pending → paid) against double-clicks and race conditions with **three stacked layers**, so the order is settled — and the confirmation email sent — **exactly once**:

| Layer | Mechanism | Purpose |
|---|---|---|
| **Layer 1** | **Atomic cache lock** (`Cache::lock('pay-order:{id}', 10)`) | A duplicate/in-flight request that can't grab the lock **bails out immediately** (stops rapid double-clicks) |
| **Layer 2** | **Pessimistic row lock** inside a `DB::transaction` (`lockForUpdate()`) | Serializes concurrent attempts at the database row level |
| **Layer 3** | **Atomic conditional flip** — `UPDATE … WHERE payment_status='pending' AND status!='cancelled'`, checking the **affected-row count** | "Single-winner" bottom guard: only the one request that finds the order still `pending` transitions it (`affected === 1`), so the email fires once |

Plus an **ownership check** (`where user_id = Auth::id()`), an **expiry check** (`isPaymentExpired()` → auto-cancel + restock), and an **explicit activity-log entry** for the paid transition (the atomic UPDATE bypasses Eloquent events, so it's logged manually to keep the audit trail complete). Covered by [PaymentHardeningTest](../tests/Feature/PaymentHardeningTest.php).

### Cancellation & Refund policy — tiered logic
Driven by two admin settings and implemented in [RefundCalculator.php](../app/Services/RefundCalculator.php):
- `CANCELLATION_FULL_REFUND_HOURS` (default **24**) — hours after payment a cancelled order still gets **100%**.
- `CANCELLATION_FEE_PERCENT` (default **10**) — fee deducted once that window passes.

**Logic (`calculate()` for a paid order being cancelled now):**
| Condition | Outcome |
|---|---|
| Not paid yet (`payment_status != 'paid'`) | No refund row — cancelling is **free** (nothing charged) |
| Paid, within full-refund window (`hours_since_paid ≤ FULL_REFUND_HOURS`) | **`full` tier → 100%** refund |
| Paid, past the window | **`fee` tier → (100 − FEE%)** refund (e.g. **90%**, 10% fee kept) |
| **Shipped / delivered / already cancelled** | **Not cancellable at all** — self-service cancel is **blocked** (a wall, not a worse tier; returns/exchange handled separately) |

- `eligibilityLabel()` renders a plain-language standing badge ("Eligible for full refund if cancelled now" / "Eligible for a 90% refund (10% fee applies)" / "Not yet paid — cancelling is free" / "Not eligible — order has shipped").
- Hours use truncated whole-hour comparison (so 24.0002 elapsed still counts as within a 24-hr window).
- On actual cancel ([MyAccountPage::cancelOrder()](../app/Livewire/MyAccountPage.php)): refund computed → order cancelled + `cancelled_at` stamped → **stock restocked** → `OrderCancelledMail` / `OrderRefundProcessedMail` sent. The public-facing policy page ([CancellationRefundPolicyPage](../app/Livewire/CancellationRefundPolicyPage.php)) explains these same settings-driven terms.

### Order & Booking trackers — guest lookup logic
Both let a **guest (no login)** check status, with a deliberately enumeration-resistant, rate-limited design:

| | Order Tracker ([OrderTracker.php](../app/Livewire/OrderTracker.php)) | Booking Tracker ([BookingTracker.php](../app/Livewire/BookingTracker.php)) |
|---|---|---|
| Route | `/track-order` | `/booking/track` |
| Lookup keys | **order number + email** (both required) | **booking reference + email** (both required) |
| Match rule | found order's `customer_email` must equal the submitted email (case-insensitive) | same — reference + matching email |
| Mismatch response | generic "email does not match" (doesn't reveal whether the order exists) | generic "no booking found" / "email does not match" |
| Null-email guard | — | a stored **null email never matches** any submitted value (can't be bypassed with blank) |
| Rate limit (per IP) | **5 / 60 s** | search **6 / 120 s**; **cancel 3 / 120 s** |
| Extra | `getStatusStepsProperty()` renders a **visual status timeline** (pending → paid → shipped → delivered) | guests can **cancel** the booking here (logged-out self-service) |

→ Two-factor lookup (id **+** matching email) + per-IP throttling means the public endpoints can't be used to enumerate or scrape orders/bookings.

**Order numbers:** `ORD-YYYY-#####` generated transactionally with `lockForUpdate` + `withTrashed` collision-avoidance ([Order.php](../app/Models/Order.php)).
**Shipping:** flat rate + free-shipping threshold from settings ([ShippingCalculator](../app/Services/ShippingCalculator.php)).
**Payment expiry:** unpaid orders auto-cancelled after 15 min + stock restocked ([ExpireUnpaidOrders.php](../app/Console/Commands/ExpireUnpaidOrders.php), runs every minute).

---

# 12. Authentication & User Management

**Files:** [UserLogin.php](../app/Livewire/Auth/UserLogin.php), [ForgotPassword.php](../app/Livewire/Auth/ForgotPassword.php), [ProfilePage.php](../app/Livewire/ProfilePage.php), [EmailOtpService.php](../app/Services/EmailOtpService.php), [SocialAuthController.php](../app/Http/Controllers/SocialAuthController.php).

| # | Feature | Implementation |
|---|---|---|
| 12.1 | Login | Combined login/register tabbed component at `/login` |
| 12.2 | Registration | Email-OTP verified: user row created **only after** 6-digit code confirmed; pending data encrypted in cache; IP-throttled 5/10 min |
| 12.3 | Password reset | OTP-based via [ForgotPassword.php](../app/Livewire/Auth/ForgotPassword.php) + EmailOtpService |
| 12.4 | Email verification | Built into the OTP registration flow (email proven before account exists) |
| 12.5 | Profile / account | `/profile` (edit info, change/set password, 2FA, delete account) + `/account` (order & booking history) |
| 12.6 | Order history | [MyAccountPage.php](../app/Livewire/MyAccountPage.php) lists user orders + bookings |
| 12.7 | Guest checkout | **Not allowed** — checkout requires auth; guest *cart* allowed and claimed on login |
| 12.8 | Session management | DB sessions, `AuthenticateSession` invalidation on password change, scheduled pruning |
| — | Email OTP engine | [EmailOtpService](../app/Services/EmailOtpService.php): **6-digit** code, **hashed in cache** (never plaintext), **10-min TTL**, **5 verify attempts** max, **60-s resend cooldown**; 5 purposes — register, password reset, set-password, login-2FA, enable-2FA |
| — | 2FA (customer) | Email-OTP login verification (`two_factor_enabled`), resend throttled 60 s, anti-flood guard |
| — | Social login | Google + Microsoft (Socialite); links to `social_accounts`; social-only accounts have null password until set via OTP |
| — | Roles | `owner`, `admin`, `staff`, `client` — `role` not mass-assignable |

### Login / Register / Logout — flow logic

**Login** ([UserLogin::login()](../app/Livewire/Auth/UserLogin.php)):
1. Validate email/password fields → resolve client IP.
2. **Lockout gate** — if a per-email+IP lockout is active, reject with remaining seconds (§4.1b).
3. Look up user; **verify with `Hash::check()`** (no session started yet). On failure: increment fail count, apply progressive lockout tier, and after 30 fails hard-block the IP.
4. On success → clear the lockout counter.
5. **If `two_factor_enabled`** → send a login OTP (resend-throttled to stop inbox flooding) and switch to the OTP step; `Auth::login()` runs only after [verifyLoginOtp()](../app/Livewire/Auth/UserLogin.php).
6. **Capture the guest session id _before_ login** (SessionGuard regenerates it), call `Auth::login($user, $remember)`, then **`CartItem::claimGuestCart()`** so the guest cart follows the user, then `session()->regenerate()` (session-fixation hardening).
7. Redirect to `url.intended` (or `/`).
- Social-login accounts with **no password** are told to use the social button / set a password instead of email login.

**Register** ([UserLogin::register()](../app/Livewire/Auth/UserLogin.php)):
1. Validate; **IP-throttle** (max 5 / 10 min).
2. **No user row is created yet** — the validated data (password **encrypted**, not plaintext) is stashed in cache and a 6-digit OTP is emailed.
3. On OTP confirm → the account is created with role **`client`** (never mass-assignable to a higher role); a **soft-deleted prior account with that email is reactivated** (returning customer) instead of duplicated.
4. Then auto-login + claim guest cart + session regenerate, as above.

**Logout** (`POST /logout`, [routes/web.php](../routes/web.php)): CSRF-protected POST only → `Auth::guard('web')->logout()` → `session()->regenerateToken()` → redirect home. (Admin panel logout is handled separately by the `admin` guard + `LogoutAdminGuardOnly`.)

### Social login → must set a password before shopping
A Google/Microsoft account starts with a **NULL password**. Such users can browse, but **checkout is gated**: [CheckoutPage::mount()](../app/Livewire/CheckoutPage.php) checks `! Auth::user()->hasPassword()` and **redirects them to `/profile`** with *"Please set a password on your account before checking out."* A site-wide amber **set-password banner** (§12 table) also nudges them. Password is set via the 2-step emailed-OTP flow in ProfilePage. → Social users **cannot proceed to shopping/checkout until a password exists**.

**Customer self-service actions** (verified in components):
| Action | Where | Detail |
|---|---|---|
| Cancel **order** | [MyAccountPage::cancelOrder()](../app/Livewire/MyAccountPage.php) | From the account dashboard; triggers refund logic ([RefundCalculator](../app/Services/RefundCalculator.php)) + restock + cancellation email. **Not** available from the public order tracker. |
| Cancel **booking** | [MyAccountPage::cancelBooking()](../app/Livewire/MyAccountPage.php) **and** [BookingTracker::cancelBooking()](../app/Livewire/BookingTracker.php) | Logged-in users from account; **guests** can cancel from the public tracker by reference. |
| Order status timeline | [OrderTracker::getStatusStepsProperty()](../app/Livewire/OrderTracker.php) | Visual step tracker (pending→paid→shipped→delivered) |
| Full **2FA lifecycle** | [ProfilePage.php](../app/Livewire/ProfilePage.php) | Enable (OTP-confirmed), disable (via **password** OR **OTP**), cancel at each step — all OTP/throttle-gated |
| Set password | ProfilePage | Social-only accounts set a password via emailed OTP (2-step) |
| Set-password banner | [layouts/app.blade.php](../resources/views/layouts/app.blade.php) | Proactive site-wide amber banner for logged-in social accounts **without** a password ("Set a password to enable shopping & checkout") — auto-hides once set (`! auth()->user()->hasPassword()`) |
| Delete account | ProfilePage | Self-service; confirmed by **password** OR **OTP** (social-only); soft-deletes the user |
| Multi-step checkout | [CheckoutPage.php](../app/Livewire/CheckoutPage.php) | `goToStep2()`/`goBack()` wizard (details → payment) |

---

# 13. Product Catalog Features

**Files:** [ProductsPage.php](../app/Livewire/ProductsPage.php), [ProductDetail.php](../app/Livewire/ProductDetail.php), [HomePage.php](../app/Livewire/HomePage.php).

| # | Feature | Status |
|---|---|---|
| 13.1 | Search bar | ✅ keyword search in ProductsPage |
| 13.2 | Category filter | ✅ dropdown filter |
| 13.3 | Price range filter | ✅ Backend min/max price filter (`COALESCE(sale_price, price)` range) in [ProductsPage.php](../app/Livewire/ProductsPage.php); input-based (not a drag slider) |
| 13.4 | Pagination | ✅ Livewire pagination |
| 13.5 | Results count | ✅ rendered in view |
| 13.6 | Sort options | Limited — default ordering; no explicit price/popularity sort UI |
| 13.7 | Product card | ✅ image, name, category badge, price, sale price, stock |
| 13.8 | Product detail | ✅ full info, specs (KeyValue), translated overview, image, quantity stepper, add-to-cart, breadcrumb |
| 13.9 | Featured products | ✅ `is_featured` homepage section |
| 13.10 | Browse by category | ✅ category quick links |
| 13.11 | Related products | ✅ Up to 4 products from the same category in [ProductDetail::render()](../app/Livewire/ProductDetail.php) |

Stock badges via colored Filament columns admin-side; storefront shows in-stock/backorder messaging.

**Vehicle compatibility:** the product detail page displays a **"Compatible Vehicles"** list from the product's `compatible_vehicles` data, and the chatbot answers "will it fit my car?" fitment questions. *(Note: an early interactive "Compatibility Checker" tool — select-your-car → match — was **simplified down to this display + chatbot Q&A**; there is no standalone interactive checker widget in the current app, and `car_models`/`product_compatibilities` have no admin UI.)*

---

# 14. Services & Booking System

**Files:** [BookingForm.php](../app/Livewire/BookingForm.php), [BookingTracker.php](../app/Livewire/BookingTracker.php), [Service.php](../app/Models/Service.php), [app/Services/Booking/](../app/Services/Booking/), [BookingResource.php](../app/Filament/Resources/Bookings/BookingResource.php).

| # | Feature | Implementation |
|---|---|---|
| 14.1 | Services page | Lists active services with price/duration. **6 seeded services:** Car Audio Installation · Subwoofer & Amplifier Setup · Window Tinting · Dashcam Installation · Car Alarm & Security System · DSP Tuning & Sound Calibration ([DatabaseSeeder.php](../database/seeders/DatabaseSeeder.php)) |
| 14.2 | Booking form fields | Multi-step: customer name, phone, email, service, date, time slot, notes, optional car model |
| 14.3 | Date/time picker | Custom calendar (month nav, closed-weekday + past-date guards) + generated time slots from business hours |
| 14.4 | Service categories | Services are flat (no separate category table) |
| 14.5 | Booking confirmation | Unique reference generated transactionally; tracker at `/booking/track` |
| 14.6 | Confirmation email | ✅ [BookingConfirmationMail](../app/Mail/BookingConfirmationMail.php), plus Confirmed/Cancelled/Reminder mailables |
| 14.7 | Admin management | [BookingResource](../app/Filament/Resources/Bookings/BookingResource.php) with status workflow (pending/confirmed/cancelled/completed) + nav badge |

**Booking logic** is settings-driven: `BUSINESS_HOURS_START/END`, `BUSINESS_CLOSED_WEEKDAYS`, `BOOKING_SLOT_MINUTES`. Day-before reminders emailed at 09:00 daily ([SendBookingReminders.php](../app/Console/Commands/SendBookingReminders.php)). Double-booking race guard applied (recent commit).

---

# 15. Multimedia & Visual Assets

| # | Asset | Detail |
|---|---|---|
| 15.1 | Hero background video | Referenced in [home-page.blade.php](../resources/views/livewire/home-page.blade.php) (autoplay/loop/muted) |
| 15.2 | Brand logos | Mohawk, 70mai, Alpine, Skynavi, Sparko (text fallback), SONY, Dynavin, MBquart — in `storage/app/public/brand-logos/` (committed) |
| 15.3 | Brand carousel | JS marquee, infinite scroll, speed-tuned |
| 15.4 | Product images | Spatie MediaLibrary `thumb`/`card` conversions |
| 15.5 | Logo variants | `logo-light.svg` / `logo-dark.svg` (theme-aware in admin + storefront) |
| 15.6 | Icons | Storefront: **Lucide** ([lucide.dev](https://lucide.dev)) icon set + brand SVG logos from **svgl.app** (WhatsApp/Facebook components); Filament admin: **Heroicons**. Hover FX patterns from **itshover.com** (see §2.3b) |
| 15.7 | Favicon / PWA icons | `winwin-favicon.svg`, `favicon.ico`, `winwin-favicon-32x32.png`, `winwin-apple-touch-icon.png`, `site.webmanifest` — cache-busted by newest mtime ([partials/favicons.blade.php](../resources/views/partials/favicons.blade.php)) |

---

# 16. Theme System (Dark / Light)

**Implementation:** class-based dark mode (`.dark` on `<html>`), Tailwind v4 `@custom-variant dark`.

| # | Aspect | Detail |
|---|---|---|
| 16.1 | Theme switcher | In nav (storefront) + Filament admin (`darkMode(true)`) |
| 16.2 | Storage | **localStorage** (`site-theme`) is source of truth, **mirrored to a cookie** (`app_theme`) so the server renders the correct class with no flash on Livewire navigation |
| 16.3 | Default | `system` (follows `prefers-color-scheme`) until user chooses |
| 16.4 | `dark:` variants | Used throughout views; brand RGB vars flip under `.dark` |
| 16.5 | `theme-color` meta | `#C8413D` (light) / `#0C0C0E` (dark) — colors mobile browser chrome |
| 16.6 | Logo swap | `logo-light.svg` ↔ `logo-dark.svg` (admin: `brandLogo` + `darkModeBrandLogo`) |

The inline head script in [layouts/app.blade.php](../resources/views/layouts/app.blade.php) resolves theme before paint (no FOUC), toggling the `.dark` class and root background color.

---

# 17. Animations & Micro-interactions

| # | Animation | Where |
|---|---|---|
| 17.1 | Scroll-triggered fade-in | AOS 2.3.4 across pages |
| 17.2 | Cubic-bezier easing | `easeInOutCubic` for 3D camera moves |
| 17.3 | Hover effects | Tailwind hover utilities on buttons/cards/images |
| 17.4 | Skeleton loaders / spinners | Filament [nav-loading.blade.php](../resources/views/filament/nav-loading.blade.php); page-loader component |
| 17.5 | Page transitions | Livewire `wire:navigate` + Filament `->spa()` progress bar |
| 17.6 | Cart drawer slide | Alpine transition |
| 17.7 | Modal open/close | Alpine transitions (confirm modal, configurator) |
| 17.8 | Brand marquee | Custom JS infinite scroll |
| 17.9 | Testimonials slider | ✅ Alpine `testimonialSlider()` carousel on the home page (auto/manual slide) |
| 17.10 | 3D rotation | OrbitControls auto/drag rotation |
| 17.11 | Image lazy-load fade | `loading="lazy"` + fade on load |
| 17.12 | Tailwind transitions | `transition-*`/`duration-*` throughout |
| — | iOS tap feedback | `touchstart` listener so Safari renders `:active` states (recent commits) |

---

# 18. Static / Legal Pages

| # | Page | File | Notes |
|---|---|---|---|
| 18.1 | About Us | [AboutPage.php](../app/Livewire/AboutPage.php) | Company intro |
| 18.2 | Contact | [ContactPage.php](../app/Livewire/ContactPage.php) | Form + Leaflet map + business info; submissions → `contacts` |
| 18.3 | Privacy Policy | [PrivacyPolicyPage.php](../app/Livewire/PrivacyPolicyPage.php) | Self-authored legal text |
| 18.4 | Terms of Service | [TermsOfServicePage.php](../app/Livewire/TermsOfServicePage.php) | Self-authored |
| 18.5 | Cancellation & Refund | [CancellationRefundPolicyPage.php](../app/Livewire/CancellationRefundPolicyPage.php) | Refund window/fee driven by settings + [RefundCalculator](../app/Services/RefundCalculator.php) |
| 18.6 | FAQ | [FaqPage.php](../app/Livewire/FaqPage.php) | Accordion of published `faqs` |

---

# 19. Contact Integrations

| # | Channel | Detail |
|---|---|---|
| 19.1 | WhatsApp | `wa.me/60169150917` (from `config('services.store.phone_raw')`). A unified, brand-green **`<x-btn.whatsapp>`** component (shine sweep + lift/active scale, [btn/whatsapp.blade.php](../resources/views/components/btn/whatsapp.blade.php)) is reused everywhere. WhatsApp links appear in the nav, footer, home, products, product detail, services, about, contact, the chatbot, **and every transactional email**. |
| 19.2 | **Context-aware prefilled WA messages** | Each entry point opens WhatsApp with text tailored to where the user clicked: |

**WhatsApp prefilled-message contexts (verified):**
| Where | Pre-filled message |
|---|---|
| Global (nav/footer) | "Hello, I would like to ask about your products and showroom visit." |
| Products page | "Hello, I would like to ask about your product range." |
| **Product detail (per-product)** | "Hi Win Win Car Studio! I'm interested in **{product name}**. Can you provide more details?" — **auto-fills the specific product's name** |
| Services page | "Hi Win Win Car Studio! I would like to ask about your installation services." |
| About page | "Hello, I would like to learn more about your products." |
| Home page CTAs | "Hello, I would like to know more about …" / "…to contact …" |
| **3D Configurator** ("Enquire Configuration") | Full **itemized build** — paint, rim style+color+price, brake color, spoiler, bumper, tint %, dash cam, base price, estimated total (§10.7) |
| Chatbot fallback / topics | "WhatsApp us at 016-915 0917" (+ location/pricing/warranty topics funnel here) |
| Transactional emails | Every order/booking email includes a "WhatsApp us" contact link |
| 19.3 | Phone call | `tel:` links using `phone_display` (016-9150917) |
| 19.4 | Email | Store email `winwincaraudio@gmail.com` (config) |
| 19.5 | Google Maps | `cid=5750306395518804732` deep link + Leaflet map (lat 3.1491 / lng 101.5465) |
| 19.6 | Facebook | `https://www.facebook.com/winwincaraudio/` (config + [btn/facebook.blade.php](../resources/views/components/btn/facebook.blade.php)) |
| 19.7 | Contact form | `contacts` table; name/email/phone/subject/message + honeypot |
| 19.8 | Newsletter signup | ❌ Not implemented |

All store contact details centralized in `config('services.store.*')` ([config/services.php](../config/services.php)).

---

# 20. Multi-Mode (Shop Mode) Features

Controlled by the `ONLINE_SHOPPING_ENABLED` setting (`true`/`false`), editable in the admin **Settings** resource.

| # | Aspect | Detail |
|---|---|---|
| 20.1 | Toggle source | `settings` table key `ONLINE_SHOPPING_ENABLED`; read via `setting()` helper |
| 20.2 | Shop Mode ON | Cart + checkout routes enabled ([ShoppingEnabled.php](../app/Http/Middleware/ShoppingEnabled.php)), cart UI shown, add-to-cart active |
| 20.3 | Shop Mode OFF (showroom) | Shopping routes redirect home with "coming soon"; cart UI hidden in nav; **new visitors see a pure showroom (no login/cart entry)**, while already-logged-in customers keep account access to view history — "soft showroom" |
| 20.4 | Admin toggle | [SettingResource.php](../app/Filament/Resources/Settings/SettingResource.php) (validated `in:true,false`); also surfaced via the confirm-toggle on the Settings table |
| 20.5 | **Graceful shutdown on close** | Turning shopping OFF fires [ShopModeService::cancelUnpaidOrders](../app/Services/ShopModeService.php) via the `Setting` model's `updated` event: every **unpaid** order is cancelled + restocked + the customer emailed, so nobody is stranded with a "pending payment" order they can no longer pay (the `/pay` route is gated). **Paid orders are never touched** — real money owed, fulfilled/refunded through the normal admin flow. Regression-guarded by [ShopModeCloseTest](../tests/Feature/ShopModeCloseTest.php) |
| 20.6 | **Customer-facing signalling** | (a) Admin-controlled site-wide **announcement bar** (`SITE_ANNOUNCEMENT_ENABLED` + `SITE_ANNOUNCEMENT_TEXT`, dismissible per-visitor keyed by message text, responsive) tells customers shopping/sign-in is paused; (b) paid orders in My Account show a "your order is not affected" reassurance line while shopping is off; (c) the dead-end "Pay now" button is hidden in showroom mode. The `ONLINE_SHOPPING_ENABLED` helper text reminds the admin to enable the announcement bar when switching off |

Consumed in: ShoppingEnabled middleware, HomePage, ProductsPage, ProductDetail, MyAccountPage, layout nav + announcement bar, ShopModeService.

---

# 21. SEO Implementation

Powered by `artesaos/seotools` ([config/seotools.php](../config/seotools.php)), generated in [layouts/app.blade.php](../resources/views/layouts/app.blade.php).

| # | Feature | Detail |
|---|---|---|
| 21.1 | Per-page meta | `SEOMeta::generate()` (title, description, keywords) |
| 21.2 | Canonical URLs | Emitted via SEOMeta |
| 21.3 | Open Graph | `OpenGraph::generate()` — og:title/description/url/type/site_name |
| 21.4 | Twitter Card | `summary_large_image` meta |
| 21.5 | meta robots | `index, follow` |
| 21.6 | theme-color | `#C8413D` / `#0C0C0E` |
| 21.7 | Structured data | `JsonLd::generate()` → `AutoPartsStore` schema enriched via [SetsSeo](../app/Livewire/Concerns/SetsSeo.php) with real local-business detail: `PostalAddress` (street/city/state/postcode/country), `telephone`, `email`, `priceRange`, `geo` (GeoCoordinates from config lat/lng), `sameAs` (Facebook), and `openingHoursSpecification` derived from the booking business-hours settings so structured hours can't drift; covered by [SeoStructuredDataTest](../tests/Feature/SeoStructuredDataTest.php) |
| 21.8 | robots.txt | [public/robots.txt](../public/robots.txt) — disallows `/admin`, declares the `Sitemap:` URL |
| — | Search Console | Ownership verified via the HTML-file method ([public/google6a58ace556856c6e.html](../public/google6a58ace556856c6e.html)); `GOOGLE_SITE_VERIFICATION` also supported as an optional meta-tag method |
| 21.9 | sitemap.xml | **Dynamically generated** by [GenerateSitemap.php](../app/Console/Commands/GenerateSitemap.php) (daily): home (1.0), products (0.9), services (0.8), booking (0.8), about/contact (0.6) + per-product URLs (0.7, weekly) |
| 21.10 | Semantic URLs | Slug-based product URLs (`/products/{slug}`), readable route names |

---

# 22. Deployment & DevOps

## Environment workflow (dev → test → production)

| Stage | Environment | Used for |
|---|---|---|
| **Develop / debug** | **Laravel Herd** (`*.test` local domain) | Day-to-day coding and debugging the site locally |
| **Test** | **`localhost:8000`** (`php artisan serve` / `npm run serve`) | Verifying flows that need a plain `http://localhost` origin — **sign-up email OTP**, **payment flow**, and **Google OAuth** (which rejects `.test` redirect URIs) |
| **Production** | **Render.com** (`winwincaraudio.onrender.com`) | The real public, HTTPS-served deployment auto-built from GitHub `main` |

> Why two local setups: Herd's `.test` domain is fastest for general development, but OAuth/OTP/payment testing is done on `localhost:8000` because Google OAuth and the OTP/redirect flows behave correctly on a standard `localhost` origin. Production then runs on Render with TiDB + cron-job.org.

| # | Aspect | Detail |
|---|---|---|
| 22.1 | Hosting | **Render.com** Web Service via **Docker** (PHP has no native Render runtime). No `render.yaml` — configured via dashboard. **Live URL: https://winwincaraudio.onrender.com** (admin at `/admin`) |
| 22.2 | Production DB | **TiDB Serverless** (MySQL-compatible, TLS-required via `MYSQL_ATTR_SSL_CA`); local dev uses SQLite |
| 22.3 | Env vars | Documented in [.env.example](../.env.example): APP_*, DB_* (mysql/TiDB), MAIL_*, GOOGLE_CLIENT_*, CRON_SECRET, DEFAULT_ADMIN_* |
| 22.4 | Build process | 3-stage [Dockerfile](../Dockerfile): (1) `composer install` → vendor/, (2) `npm install` + `vite build` (needs vendor for Filament theme.css), (3) PHP 8.4 runtime w/ pdo_mysql, gd, zip, bcmath, intl, exif + image optimizers |
| 22.5 | Static assets | `php artisan storage:link` on boot; `storage/app/public` committed to git (Render has no persistent disk) |
| 22.6 | Continuous deployment | Auto-deploy from GitHub `main` on push (Render native) |
| 22.7 | Error monitoring | Laravel multi-channel logging + DB `app_logs` table viewable in admin **Logs** + **System Status** page. No Sentry/Bugsnag |
| 22.8 | Backup strategy | **Dev:** a manual SQLite snapshot exists (`database/database.sqlite.backup-…`, git-ignored, taken before the pre-launch data wipe). **Prod:** relies on **TiDB Serverless's built-in managed backups** (automatic snapshots / PITR). ⚠️ **No application-level backup job** in the repo (no `spatie/laravel-backup`/`mysqldump` cron) — recommended future hardening for an independent off-provider copy. |
| — | Scheduler | No native cron on Render free tier → external **cron-job.org** pings `/cron/run-schedule/{CRON_SECRET}` every 10 min, running `schedule:run` (also keeps the instance warm) |
| — | Boot tasks | [docker-entrypoint.sh](../docker-entrypoint.sh): storage:link, migrate --force, config/route/view cache, `php artisan serve` on `$PORT` |

**Production secrets/setup completed this engagement:** Render env vars, TiDB connection + data migration from SQLite, cron-job.org schedule, Google OAuth (social login), Gmail-API send setup, email-OTP 2FA.

---

# 23. Browser & Device Compatibility

| # | Aspect | Detail |
|---|---|---|
| 23.1 | Target browsers | Modern evergreen (Chrome, Safari, Firefox, Edge); WebGL2 with WebGL/experimental fallback for 3D |
| 23.2 | PWA | `site.webmanifest` present; **no service worker** → not installable/offline |
| 23.3 | Mobile gestures | Pinch-to-zoom + drag-rotate on 3D (OrbitControls) |
| 23.4 | Touch vs mouse | `touchstart` listener for iOS `:active`; pointer-friendly hit areas |
| 23.5 | Viewport meta | `width=device-width, initial-scale=1.0` |
| — | Progressive fallback | `overflow: clip` → `hidden` fallback; webgl capability gate disables configurator gracefully |

---

# 24. Form Handling Patterns

| # | Aspect | Detail |
|---|---|---|
| 24.1 | Forms in app | Booking, contact, login/register, forgot-password, OTP verify, checkout, profile/password, set-password, delete-account |
| 24.2 | Real-time validation | Livewire `wire:model` + `validate()` / per-step validation in BookingForm |
| 24.3 | Error display | `@error`/error bags, inline messages, `addError()` |
| 24.4 | Success feedback | `session()->flash()`, Livewire dispatch events, redirects, toasts |
| 24.5 | Honeypot / spam | `spatie/laravel-honeypot` (`UsesSpamProtection` + `HoneypotData`) on **4 forms**: Login/Register ([UserLogin](../app/Livewire/Auth/UserLogin.php)), Forgot-Password ([ForgotPassword](../app/Livewire/Auth/ForgotPassword.php)), Booking ([BookingForm](../app/Livewire/BookingForm.php)), Contact ([ContactPage](../app/Livewire/ContactPage.php)) — randomized field name + timing check; plus OTP + IP rate-limits on register |
| 24.6 | Field types | text, tel, email, password, date, time, select, textarea, checkbox |

---

# 25. Admin Panel (Filament) — Full Detail

Provider: [AdminPanelProvider.php](../app/Providers/Filament/AdminPanelProvider.php). Filament v5.5.

## 25.1 Authentication & Access Control
- **Login:** `/admin/login` → custom [Pages/Auth/Login.php](../app/Filament/Pages/Auth/Login.php).
- **Access gate:** [User::canAccessPanel()](../app/Models/User.php) → `isAdmin() || isStaff()` (owner/admin/staff; clients blocked).
- **Roles & permissions:** custom role column + 12 Laravel **Policies** ([app/Policies/](../app/Policies/)). **No** Spatie Permission / Filament Shield.
- **Guards:** separate `admin` auth guard ([config/auth.php](../config/auth.php)); customer site uses `web`.
- **2FA:** Filament `multiFactorAuthentication([AppAuthentication::make()->recoverable()])` — **TOTP app authenticator with recovery codes** (opt-in via profile).
- **Session:** `AuthenticateSession` middleware; `LogoutAdminGuardOnly` custom middleware.
- **Login rate limiting:** Filament built-in throttling.
- **Password policy:** centralized `Password::defaults()` (8+ chars, mixed case, numbers, symbols, leaked-password/HaveIBeenPwned check) — applies to admin user creation too (see §4.6b).

## 25.2 Dashboard
Custom [Dashboard.php](../app/Filament/Pages/Dashboard.php) overrides `getWidgets()` and `getColumns()` (responsive grid: **default 1 / sm 2 / md 4 / xl 12** columns). Widget layout (Z-pattern), in order:
- **Row 1 — StatsOverview** ([StatsOverview.php](../app/Filament/Widgets/StatsOverview.php)) — KPI cards: Active Products, Total Bookings (+pending awaiting confirmation), etc., w/ description icons. Cached 60 s.
- **Row 2 — RevenueChart** (line, time-range filters) + **CategoryDistributionChart** (doughnut).
- **Row 3 — TopProductsChart** (stock bar chart, full-width).
- **Row 4 — RecentOrdersWidget** (table — order #, customer, email, items, total, status, payment).
- **Row 5 — RecentActivityWidget** (table — log, action, model, by, when; from Spatie ActivityLog).
- **AccountWidget** (Filament built-in) also registered in the panel.

## 25.3 Resources (each audited)

### Products — [ProductResource.php](../app/Filament/Resources/Products/ProductResource.php)
- **Form** ([ProductForm.php](../app/Filament/Resources/Products/Schemas/ProductForm.php)): 8× TextInput, 5× Textarea (incl. EN/MS/ZH descriptions), 3× Toggle (is_active, is_featured, has_3d), 2× TagsInput, 2× SpatieMediaLibraryFileUpload (image), 2× Select (category relationship, brand), 2× KeyValue (specs, compatible_vehicles); required/numeric/maxLength rules. Auto-slug on create.
- **Table** ([ProductsTable.php](../app/Filament/Resources/Products/Tables/ProductsTable.php)): ImageColumn; searchable/sortable name (w/ SKU description + tooltip); category badge; brand (`visibleFrom('md')`); price + sale_price (MYR); **color-coded stock** (danger/warning/gray + tooltip); inline ToggleColumns for is_active/is_featured (admin-only); slug/created/updated toggleable-hidden.
- **Filters:** category (multiple, preload), brand (multiple, distinct), is_active (ternary), on-sale (toggle), low-stock <5 (toggle).
- **Bulk actions:** Activate, Deactivate (confirm), Feature, Unfeature, **Export** (ProductExporter), Delete (admin-only).
- **Row actions:** Edit. **Pagination:** [10,25,50,100,all]. Striped. Filters/sort/search **persisted in session**.
- **"Auto Generate Description" action** ([EditProduct.php](../app/Filament/Resources/Products/Pages/EditProduct.php)) — header action (confirmation modal) that calls `ChatServiceInterface::generateDescription($record)` to compose a product description from the product's own data (brand, category, specs) via the keyword chat service. Auto-slug on create.
- **Nav group:** Store Products.

### Categories — [CategoryResource.php](../app/Filament/Resources/Categories/CategoryResource.php)
- Form: name, slug (auto), description, image (FileUpload), is_active Toggle, sort_order. Flat (no nesting). Cascade-protect on delete (boot). Nav group: Store Products.

### Brands — [BrandResource.php](../app/Filament/Resources/Brands/BrandResource.php)
- Form: name, logo (FileUpload image), display_type (text/image), website_url, sort_order, is_active. Drives homepage marquee. Nav group: Store Products.

### Orders — [OrderResource.php](../app/Filament/Resources/Orders/OrderResource.php)
- Form: 19× TextInput (mostly **disabled/read-only**), Sections, status Select, payment fields, shipping address, cancellation fields, lifecycle placeholders; conditional `->visible()` fields. **No create** (`canCreate=false` — orders come from checkout).
- **Status workflow:** `pending → processing → shipped → delivered`, plus `cancelled` (exact values from `Order::statuses()`; forward-only chain enforced; mirrors customer-facing). Separate `payment_status`. Status-change emails (Confirmation/Shipped/Delivered/Cancelled/RefundProcessed mailables).
- **Export:** OrderExporter (bulk). **Nav badge:** order counts. Nav group: Sales.

### Bookings — [BookingResource.php](../app/Filament/Resources/Bookings/BookingResource.php)
- Form: customer name/phone/email, service Select, date + time (DateTimePicker/DatePicker), notes, status. Status management (pending/confirmed/cancelled/completed). Nav badge (pending count). Nav group: Sales.
- **Custom row actions:** `Confirm`/`confirmBooking` (confirm a pending booking → sends confirmation email), `sendReminder` (manually fire the day-before reminder), plus Edit — with confirmation prompts. Status-change mailables (Confirmed/Cancelled/Reminder).

### Testimonials (Feedback) — [FeedbackResource.php](../app/Filament/Resources/Feedback/FeedbackResource.php)
- Form: customer name, rating (numeric), content, image (FileUpload), source, **is_featured toggle** (show on homepage), sort_order. Model labels customized to "Testimonial". Nav group: Customer Interactions.

### Contacts — [ContactResource.php](../app/Filament/Resources/Contacts/ContactResource.php)
- Form: name, email (tel/email validated), phone, subject, message, is_read toggle. Inbox for contact-form submissions. Nav badge (unread). Nav group: Customer Interactions.

### Customers — [CustomerResource.php](../app/Filament/Resources/Customers/CustomerResource.php)
- Read-oriented client directory (`canCreate`/`canDelete` controlled). Nav group: Customer Interactions.

### Users — [UserResource.php](../app/Filament/Resources/Users/UserResource.php)
- Form: name, email (unique), phone (tel), role Select, etc., in Sections. Admin/staff account management. Nav group: System Settings.

### FAQ Page — [FaqResource.php](../app/Filament/Resources/Faqs/FaqResource.php)
- Public FAQ content CRUD (question/answer + translations, sort_order, is_published). Nav group: System.

### Chatbot FAQs — [ChatbotFaqResource.php](../app/Filament/Resources/ChatbotFaqs/ChatbotFaqResource.php)
- Chatbot knowledge base (question/answer/keywords/priority/is_active) — the live source of truth for the assistant. Nav group: System.

### Activity Log — [ActivityResource.php](../app/Filament/Resources/Activities/ActivityResource.php)
- Read-only audit trail (`canCreate=false`) of who-did-what (Spatie ActivityLog). View/infolist pages.

### Logs — [LogResource.php](../app/Filament/Resources/Logs/LogResource.php)
- Application error log viewer (`app_logs`); "Check for recurrence" / "Mark fixed" actions; nav badge (unresolved count). Nav group: System.

### Settings — [SettingResource.php](../app/Filament/Resources/Settings/SettingResource.php)
- Key-value editor with per-key labels, validation, placeholders, helper text. **Keys:** ONLINE_SHOPPING_ENABLED, BUSINESS_HOURS_START/END, BUSINESS_CLOSED_WEEKDAYS, BOOKING_SLOT_MINUTES, BACKORDER_DAYS, SHIPPING_FLAT_RATE, SHIPPING_FREE_THRESHOLD, CANCELLATION_FULL_REFUND_HOURS, CANCELLATION_FEE_PERCENT. Nav group: System.

### 3D Configurator options
**Hardcoded** in [configurator.js](../resources/js/configurator.js) — **not** admin-manageable.

## 25.4 Custom (non-CRUD) pages
- **Settings** (key-value, above).
- **System Status** ([SystemStatus.php](../app/Filament/Pages/SystemStatus.php)) — health checks: Your data (DB), Website speed, Background tasks, Failed tasks, Automatic tasks (cron heartbeat), Email sending, Server space, Recent problems, Developer mode; environment/version/Laravel/PHP info; recent errors list. Cards link to the Logs resource.
- **Activity Log viewer** (resource).
- Maintenance-mode toggle: **Not a dedicated page** (Laravel maintenance via artisan; Shop Mode covers the storefront on/off case).
- Reports/Analytics page: **Not implemented** (dashboard charts cover this).

## 25.5 Form components used
TextInput, Textarea, Select, SpatieMediaLibraryFileUpload, FileUpload, Toggle, TagsInput, KeyValue, DatePicker, DateTimePicker, Placeholder, Hidden, Section/Fieldset grouping. RichEditor/MarkdownEditor assets are bundled by Filament but the resources above primarily use Textarea. **Not used:** ColorPicker, Repeater/Builder, Wizard (in these resources).

## 25.6 Table features
Searchable + sortable columns; SelectFilter / TernaryFilter / custom Filter (toggle); BulkActionGroup (activate/deactivate/feature/unfeature/export/delete); row EditAction + custom actions (e.g. Bookings Confirm/Send Reminder); header/toolbar actions; striped rows; **session-persisted** filters/sort/search; responsive column hiding (`visibleFrom`, `toggleable`); ImageColumn, ToggleColumn, color/weight-coded TextColumn; **CSV/Excel export** (Product/Order exporters) + **import** (Product/Order importers). **Drag-drop reorder** (`->reorderable('sort_order')` + `HasSortableOrder`) on **Brands, Categories, Testimonials**.

## 25.7 Notifications & feedback
- Toast notifications (success/warning/danger) on actions.
- **Database notifications** (`->databaseNotifications()`) — bell icon + history, backed by `notifications` table (per-user isolation).
- Import/export completion persisted to the acting user's bell via [NotifiesImportExportCompletion](../app/Filament/Concerns/NotifiesImportExportCompletion.php) trait.
- Real-time (Echo/Reverb): **Not implemented** (`BROADCAST_CONNECTION=log`).

## 25.8 Activity logging / Audit trail (Spatie)
- **9 models with `LogsActivity`:** User (password excluded), Product, Category, Order, Booking, Feedback, Contact, Setting, Service.
- Logs **created/updated/deleted** events with dirty-attribute diffs (old → new); **causer** (who) + **IP address** + timestamp captured per entry.
- **Viewer:** Activity resource ([ActivitiesTable.php](../app/Filament/Resources/Activities/Tables/ActivitiesTable.php)) shows `causer.name`, `ip_address`, action, model, when — full **who-did-what-when** audit trail (read-only, `canCreate=false`).
- **Retention:** trimmed to the 5,000 most-recent records daily (`activitylog:trim --keep=5000`, [TrimActivityLog.php](../app/Console/Commands/TrimActivityLog.php)).
- Complemented by the **observability log trail** (trace IDs + `app_logs`, see §35.5) for system/error auditing.

## 25.9 Permissions & policies — **Role × Permission Matrix**

**Roles** (on `users.role`): `owner` · `admin` · `staff` · `client`. Helpers in [User.php](../app/Models/User.php): `isOwner()`; `isAdmin()` = **admin OR owner**; `isStaff()`; `canAccessPanel()` = `isAdmin() || isStaff()` (so **clients are fully locked out** of `/admin`). Enforced by 12 policies in [app/Policies/](../app/Policies/).

### What each role can do in the admin panel

| Capability | Owner | Admin | Staff | Client |
|---|:---:|:---:|:---:|:---:|
| Access `/admin` at all | ✅ | ✅ | ✅ | ❌ |
| **View** content (Products, Orders, Bookings, Testimonials, Contacts, Categories, Brands, Services) | ✅ | ✅ | ✅ (read-only) | ❌ |
| **Create / Edit / Delete** that content | ✅ | ✅ | ❌ | ❌ |
| **Restore** soft-deleted content | ✅ | ✅ | ❌ | ❌ |
| **Force-delete** (permanent) content | ✅ | ❌ | ❌ | ❌ |
| **Users** — view/create/edit | ✅ | ✅ | ❌ | ❌ |
| Edit/delete the **owner** account | ✅ | ❌ | ❌ | ❌ |
| Delete **own** account mid-session | ❌ (blocked) | ❌ (blocked) | — | — |
| Force-delete any user | ❌ (nobody) | ❌ | ❌ | ❌ |
| **Settings** — view & edit values | ✅ | ✅ | ❌ | ❌ |
| **Settings** — create / delete keys | ✅ | ❌ | ❌ | ❌ |
| **FAQ / Chatbot FAQs** — manage | ✅ | ✅ | ❌ (can't even view) | ❌ |
| Force-delete Chatbot FAQ | ✅ | ❌ | ❌ | ❌ |

### The exact rules (per policy)
- **Standard content** (Product, Order, Booking, Feedback/Testimonial, Contact, Category, Brand, Service) — `viewAny`/`view` = admin **or staff**; `create`/`update`/`delete`/`restore` = **admin only**; `forceDelete` = **owner only**. → *Staff get read-only access; only admins mutate; only the owner can permanently destroy.*
- **User** ([UserPolicy](../app/Policies/UserPolicy.php)) — all actions admin-only, **plus**: an admin **cannot edit/delete the owner** (only the owner can); **nobody can delete their own account** mid-session; `forceDelete` is **always false** (no one can hard-delete a user).
- **Setting** ([SettingPolicy](../app/Policies/SettingPolicy.php)) — `view`/`update` = admin; but `create`/`delete`/`restore`/`forceDelete` = **owner only** (admins can change values, only the owner can add/remove setting keys).
- **Faq / ChatbotFaq** — **admin-only for everything incl. viewing** (staff cannot see them at all); ChatbotFaq `forceDelete` = owner.

### Reinforced in the UI
Policies are mirrored by explicit `authorize()`/`isAdmin()` checks in tables so staff don't even see the controls: inline ToggleColumns (is_active/is_featured) are `disabled` for non-admins; bulk Activate/Deactivate/Feature/Export/**Delete** are admin-gated; Orders/Users bulk-delete wired to `UserPolicy`. Tested by [StaffBulkDeleteAuthorizationTest](../tests/Feature/StaffBulkDeleteAuthorizationTest.php) + [UserAdminAuthorizationTest](../tests/Feature/UserAdminAuthorizationTest.php) (admin can self-demote but **not** self-promote to owner).

## 25.10 Localization in admin
Admin UI uses Filament's English plus `__()`-wrapped custom strings (some widget labels translatable). Primarily **English**; storefront is fully tri-lingual.

## 25.11 Admin UI/UX
- Custom branding: brand name "Win Win Car Audio", light/dark logos, Ember-red primary ramp, DM Sans font, `admin.css` responsive overrides.
- Navigation groups: **Sales · Store Products · Customer Interactions · System Settings (collapsed) · System (collapsed)**.
- Heroicon navigation icons; nav badges (pending bookings/orders, unread contacts, unresolved logs).
- Dark/light mode enabled; SPA navigation with skeleton loaders; scroll-to-top button; mobile-responsive collapsible sidebar.

## 25.12 Admin security measures
- Route protection: `Authenticate` + `LogoutAdminGuardOnly` + `AdminMiddleware`.
- Audit trail for admin actions (ActivityLog).
- Password field hashed via `dehydrateStateUsing(Hash::make)`; confirmation step before post-password-change logout ([EditProfile.php](../app/Filament/Pages/Auth/EditProfile.php)).
- Delete confirmations (`requiresConfirmation`). CSP intentionally skipped on `/admin` so Filament assets work.
- IP whitelist: **Not implemented**.

## 25.13 Imports / Exports (full detail)

Two resources support bulk CSV/Excel import **and** export — **Products** and **Orders**. All importer/exporter jobs force `getJobConnection() => 'sync'` (no queue worker needed), and completion results are pushed to the acting user's notification bell via the [NotifiesImportExportCompletion](../app/Filament/Concerns/NotifiesImportExportCompletion.php) trait (success/warning/danger by failed-row count). Import tracking uses the `imports` / `exports` / `failed_import_rows` tables (Filament built-ins).

**Where the actions live:**
| Action | Location | Scope |
|---|---|---|
| Export-all (header) | [ListProducts.php](../app/Filament/Resources/Products/Pages/ListProducts.php), [ListOrders.php](../app/Filament/Resources/Orders/Pages/ListOrders.php) | `ExportAction` — exports the whole (filtered) table |
| Import (header) | ListProducts / ListOrders | `ImportAction` — upload CSV to create/update |
| Export-selected (bulk) | [ProductsTable.php](../app/Filament/Resources/Products/Tables/ProductsTable.php), [OrderResource.php](../app/Filament/Resources/Orders/OrderResource.php) | `ExportBulkAction` — exports only checked rows; **admin-only** (`authorize(isAdmin)`) |

### Product Export — [ProductExporter.php](../app/Filament/Exports/ProductExporter.php)
Columns: SKU, Name, Brand, Category Slug, Category Name, Price, Sale Price, Stock, Short Description, Description, Is Active (1/0), Is Featured (1/0). Description fields run `formatStateUsing` to collapse embedded newlines into single spaces (prevents broken Excel row heights). Booleans normalized to `1`/`0`.

### Product Import — [ProductImporter.php](../app/Filament/Imports/ProductImporter.php)
Columns + validation: **SKU** (required mapping, upsert key), **Name** (required, ≤255), Brand (nullable ≤255), **Category Slug** (required — resolves/links category), **Price** (required, numeric ≥0, 2dp), Sale Price (nullable numeric), Stock (nullable integer ≥0), Short Description / Description (nullable string), Is Active / Is Featured (boolean).

**`resolveRecord()` logic (UPSERT):**
1. Read `sku` from the row. If **blank → create a brand-new `Product`** (Filament then fills validated columns).
2. Otherwise look up an existing product by **case-insensitive SKU** (`LOWER(sku) = ?`).
3. **Found → update** that product with the row's values; **not found → create** a new one.
→ Re-importing the same SKU edits the existing product (no duplicates); new SKUs are added. Category is linked by slug. Each row is validated independently; bad rows land in `failed_import_rows`.

### Order Export — [OrderExporter.php](../app/Filament/Exports/OrderExporter.php)
Columns: Order Number, Status, Payment Status, Customer Name, Customer Email, Customer Phone, Tracking Number, Shipping Street/City/Postcode/State, Subtotal, Shipping Fee, Total Amount, Created At. **Date-range filter** on the export form (`fromDate` / `untilDate` DatePickers).

### Order Import — [OrderImporter.php](../app/Filament/Imports/OrderImporter.php)
Columns + validation: **Order Number** (required mapping, match key), Status (nullable — restricted to valid statuses **excluding `cancelled`**), Tracking Number (≤100), Customer Name (≤255), Customer Phone (≤50), Street/City/Postcode/State (nullable). Toggle: **"Email customers about status changes"**.

**`resolveRecord()` logic — UPDATE-ONLY** (imports never create orders):
1. Read `order_number`; look up by **case-insensitive** match (`LOWER(order_number) = ?`).
2. **Not found → throws `RowImportFailedException`** ("No order found with order number …") → that row is logged in `failed_import_rows`, the rest continue.
3. Found → applies the mapped fields (status, tracking, customer/shipping details).

**`afterSave()` email side-effect:** if the "Email customers about status changes" toggle is on (default true) **and** the order has a `customer_email`, the matching status mailable is sent; failures are caught + logged (never aborts the import). Status excludes `cancelled` so an import can't silently cancel + wrongly email.

**`beforeValidate()` forward-only status guard:** the bulk import enforces the *exact same* state machine as the single-row `markPaid`/`markShipped`/`markDelivered` actions (25.3) — a row can only move a status to `processing` if the order is currently `pending` **and** `payment_status=paid`, to `shipped` only from `processing`, and to `delivered` only from `shipped`; any other target throws `RowImportFailedException` and the row fails individually. Without this, a row could jump a never-paid order straight to `delivered` (wrongly emailing the customer) or regress a `delivered` order back to `processing` while `shipped_at`/`delivered_at` stayed stamped. Re-importing a row with its current, unchanged status is a no-op, not a rejected transition. Covered by [OrderImporterTest](../tests/Feature/OrderImporterTest.php).

> **No** dedicated full-database backup/restore feature (see 25.21).

## 25.14 Media library management
Spatie MediaLibrary via Filament plugin (`SpatieMediaLibraryFileUpload`); conversions (thumb/card) auto-generated on upload using installed optimizer binaries. Config ([config/media-library.php](../config/media-library.php)): `public` disk, **10 MB** max upload, conversions run **synchronously** (`queue_conversions_by_default=false`) — important since prod has no queue worker. No storage-usage stats / orphan-cleanup UI (orphaned media cleaned via model `forceDeleted` hooks, e.g. Feedback).

## 25.15 SEO management
Per-product slug + meta handled at the storefront/SEOTools layer; sitemap regenerated daily (and on demand via `sitemap:generate`). No in-admin Google-snippet preview editor.

## 25.16 Customer communication
- Transactional emails (order/booking lifecycle) via mailables.
- WhatsApp/email links use centralized store config.
- Contacts inbox: mark read/unread. No "send WhatsApp/email from admin" composer UI.

## 25.17 Reports & analytics
Dashboard charts (revenue, top products, category distribution) + stats. No external analytics (Google Analytics/Plausible) and no date-range report export page.

## 25.18 Admin user management
- Multiple admin accounts via Users resource; roles owner/admin/staff.
- Admin profile page (edit + password-change confirmation + TOTP 2FA).
- Last-login tracking: **Not implemented** (activity log provides action history instead).

## 25.19 System settings
Store identity/contact/social/hours live in `config('services.store.*')` (env-overridable). Operational settings (shop mode, business hours, slots, shipping, refund policy) live in the `settings` table via the Settings resource. Email via Gmail-API transport + SMTP config. Default language = EN. No currency/tax settings table (MYR hardcoded in formatting; no tax logic).

## 25.20 Filament plugins used
- **filament/spatie-laravel-media-library-plugin** ^5.5 (media uploads).
- Spatie ActivityLog + MediaLibrary core packages (surfaced in Filament via custom resources, not separate Filament plugins).
- **No** Filament Shield, **No** Filament Curator.

## 25.21 Database backup & restore
No in-app backup/restore. Dev: manual SQLite file backups. Prod: TiDB managed service. (Recommend documenting a `mysqldump`/TiDB export routine for the report.)

## 25.22 Filament-specific features
- **Global search:** **Not configured** — no resource defines `getGloballySearchableAttributes()`/`$recordTitleAttribute`, so the global (Cmd+K) search bar is effectively unused. Per-table column search is used instead.
- **Tenancy:** Not used (single tenant).
- **Dashboards:** Single dashboard.
- **Sections/Fieldsets:** Used (Orders, Users, Feedback forms).
- **Conditional fields:** `->visible()` on Order fields.
- **Cascading selects:** Category/brand relationship selects (no dependent sub-category cascade).
- **SPA mode:** `->spa()` site-wide with custom loading overlay.

---

## Appendix — Scheduled tasks ([routes/console.php](../routes/console.php))
| Command | Frequency | Purpose |
|---|---|---|
| `sitemap:generate` | daily | Regenerate sitemap.xml |
| `model:prune` (ChatLog/AppLog/CartItem) | daily | Trim self-cleaning tables |
| prune-expired-sessions | daily | Delete stale DB sessions |
| `queue:prune-failed` | daily | Clean failed_jobs (>30 d) |
| scheduler-heartbeat | every minute | Powers System Status "Automatic tasks" check |
| `activitylog:trim` | daily | Keep newest 5,000 activity rows |
| `orders:expire-unpaid` | every minute | Cancel unpaid orders >15 min, restock |
| `bookings:send-reminders` | daily 09:00 | Email day-before booking reminders |
| `logs:auto-resolve` | daily 03:00 | Auto-resolve silent error logs |

*(Driven in production by cron-job.org hitting `/cron/run-schedule/{CRON_SECRET}` every 10 minutes.)*

---

# 26. Version Control, Tooling & Collaboration

The project was built by a **team using Git + GitHub for collaborative development**, with **Visual Studio Code** as the editor.

## 26.1 Version control (Git)
- **355 commits on `main`** (516 across all branches incl. a safety branch), spanning **2026-04-06 → 2026-06-26** (~2.5 months of active development by the team).
- **Conventional Commits** convention followed throughout — `type(scope): summary`. Distribution: **192 `fix`**, **131 `feat`**, 17 `perf`, 16 `refactor`, 11 `style`, 11 `chore`, 8 `docs`, 4 `test`. This gives a clean, scannable change history.
- Branching: `main` (primary) + safety branch (`backup/main-before-origin-reset-…`) taken before a history reset — evidence of deliberate, recoverable version management.
- The commit history doubles as the project **changelog / audit trail** (who changed what, when, and why — every message explains the rationale).

## 26.2 Collaboration (GitHub)
- Hosted on GitHub: **`github.com/yhh220/capstone`**.
- **Multi-developer team — 4 contributors:**

| Contributor | Commits |
|---|---|
| Yi Hang (Chin Yi Hang) | ~466 |
| Shee Zhen Hong | ~25 |
| Soon Heng | ~17 |
| (yhh220) | 1 |

- GitHub is the single source of truth: team members `git pull`/`push` to sync work across devices, and **production auto-deploys from `main`** on every push (Render integration — see §22).

## 26.3 Editor / IDE (VS Code)
- **Visual Studio Code** is the development editor (this audit and the codebase were authored/maintained in VS Code, including its native AI assistant integration).
- `.vscode/` is intentionally **git-ignored** (per-developer local IDE settings stay out of the shared repo — standard practice).

## 26.4 Local tooling
- **Laravel Herd** for the local PHP/web stack (`.test` domains) + `php artisan serve` on `localhost:8000` for OTP/payment/OAuth testing (see §22 workflow).
- **Vite** dev server (`npm run dev` / `npm run serve`) for hot asset reloading.
- **Laravel Pint** for code-style consistency; **PHPUnit** for the test suite (Section 40).

---

# 29. Keyword-Based Chatbot ★

> **Important:** This is a **keyword/intent-matching assistant — NOT an AI/LLM.** It was intentionally downgraded from any AI approach to a deterministic keyword knowledge base. The active service is [MockDriver.php](../app/Services/Chat/MockDriver.php), bound as a singleton in [ChatServiceProvider.php](../app/Providers/ChatServiceProvider.php) behind [ChatServiceInterface](../app/Contracts/ChatServiceInterface.php). The Livewire UI is [Chatbot.php](../app/Livewire/Chatbot.php).

| # | Aspect | Implementation |
|---|---|---|
| 29.1 | Approach | Priority-ranked keyword matching (rule list with `priority`; highest-scoring rule wins). No ML, no external API. |
| 29.2 | Keyword dictionary / intent map | `builtinKnowledge()` in MockDriver — each rule = `priority` + `keywords[]` (mixed EN/MS/ZH) + per-language reply. Admin-editable copy lives in `chatbot_faqs`. |
| 29.3 | Match strategy | Tiered: ≤3-char Latin words need whole-word boundary; 4-char need leading boundary (stem); longer phrases + CJK use substring `str_contains`. Case-insensitive. |
| 29.4 | Response templates | ~20+ topics, each with EN/MS/ZH variants: greetings, hours, location, booking, pricing, warranty, aircond, takeaway/pickup, audio, tint, dashcam, wrap/PPF, products, installation, payment, delivery, contact, privacy, terms, vehicle fitment. |
| 29.5 | Fallback | On no clear match or a thrown error → "Connection issue / WhatsApp us at 016-915 0917" in the user's language. Location/pricing answers also funnel to WhatsApp. |
| 29.6 | Chat UI | Floating popup widget injected globally via `@livewire('chatbot')` in the layout; open/close, language selector, quick-reply chips, message bubbles. |
| 29.7 | History persistence | **Session-based** (`session('chatbot')` via `persist()`); capped at 40 retained messages, 20 used as recent context. |
| 29.8 | Languages | Trilingual EN/MS/ZH — keywords share one list per rule; replies are per-language; per-message language auto-detection (`detectLang()`, incl. Traditional→Simplified). |
| 29.9 | Queries covered | Location/hours, product availability, pricing, booking process, installation, brand inquiries (Mohawk/70mai/Alpine… pulled live from `brands`), contact info, vehicle compatibility/fitment, services (pulled live from `services`), policy questions. |
| 29.10 | WhatsApp hand-off | Fallback + several topics output `wa.me` guidance with the store number; navigation CTAs deep-link to relevant pages. |
| 29.11 | Quick replies | `suggestions()` returns 6 chips per language (Book appointment, Products, Opening hours, Location, Car audio, Pricing) with lucide icons; `quickAsk()` fires them without typing. |
| 29.12 | Conversation flow | Mostly **stateless single-turn**; light state for language choice + "what page am I on?" context awareness (uses captured route). |
| 29.13 | Admin manage keywords | ✅ **Filament resource** [ChatbotFaqResource](../app/Filament/Resources/ChatbotFaqs/ChatbotFaqResource.php) — DB `chatbot_faqs` is the source of truth (cached 1 h); built-in knowledge is the fresh-DB fallback + seed source. |
| 29.14 | Typo handling | Stem/boundary tiering tolerates suffixes ("book"→"booking"); Traditional→Simplified normalization; mixed-language matching. |
| 29.15 | Logging | `chat_logs` table via `ChatLog::record()` (prunable 90 days) for later keyword tuning. |
| — | Abuse protection | Input `strip_tags` + 500-char cap; rate limits 12/min + 120/hour per IP; 4-strike → 10-min cooldown block. |
| — | Input moderation/security | `classifyInput()` screens each message → **`security`** (script/SQL/PHP injection, `${...}`/`{{...}}` template injection, code payloads, spam/gambling links) vs **`inappropriate`** vs clean. Malicious input gets a `safetyMessage()`, inappropriate gets a `moderationMessage()`, and **blocked input is never echoed back** into the transcript. |

---

# 31. Laravel Application Layer

Scan of [app/](../app/) subfolders:

| # | Layer | Status / Detail |
|---|---|---|
| 31.1 | Models | 20 models in [app/Models/](../app/Models/) (+ `Concerns/HasSortableOrder`). See Section 5 for relations/casts. Accessors/mutators: `Product::getCurrentPriceAttribute`, `getIsOnSaleAttribute`, `getTranslatedDescriptionAttribute`, `getImageUrl`; `Booking::getStatusColorAttribute`; `Order::getNextStatusAttribute`; `Category::getIconAttribute`; `Service::getDurationLabelAttribute`. Casts: arrays/json, booleans, `decimal:2`, `hashed` password, datetimes. |
| 31.2 | Controllers | 4 in [app/Http/Controllers/](../app/Http/Controllers/): base Controller, **InvoiceController** (HTML+PDF invoice), **SocialAuthController** (OAuth), **GmailSendSetupController** (one-time Gmail OAuth). Most logic is in Livewire instead. |
| 31.3 | Livewire components | 21 in [app/Livewire/](../app/Livewire/), including `Auth/UserLogin` and `Auth/ForgotPassword` (plus 2 non-component traits in `Concerns/`). Each maps to a route or is globally injected (Chatbot). See Section 1.1. |
| 31.4 | Form Requests | **None** ([app/Http/Requests/](../app/Http/) does not exist) — validation lives inline in Livewire components and Filament form schemas. |
| 31.5 | Middleware | 6 custom in [app/Http/Middleware/](../app/Http/Middleware/): `AdminMiddleware`, `AssignTraceId`, `LogoutAdminGuardOnly`, `SecurityHeaders`, `SetLocale`, `ShoppingEnabled`. |
| 31.6 | Service Providers | [app/Providers/](../app/Providers/): `AppServiceProvider`, `ChatServiceProvider` (binds chatbot driver), `Filament/AdminPanelProvider`. |
| 31.7 | Events & Listeners | **None** (`app/Events`, `app/Listeners` absent) — model boot hooks + scheduled commands used instead. |
| 31.8 | Jobs / Queues | **No custom Jobs** (`app/Jobs` absent). `QUEUE_CONNECTION=database`, but no `queue:work` process runs anywhere — the one real `ShouldQueue` class in the app is Spatie Media Library's conversion job, forced to run synchronously instead (`queue_conversions_by_default=false`, see 9 / 25.14). Filament import/export also run synchronously (`getJobConnection() => 'sync'`). |
| 31.9 | Mail classes | 10 mailables in [app/Mail/](../app/Mail/) + `Transport/GmailApiTransport`. See Section 32. |
| 31.10 | Notifications | 1: [EmailOtp](../app/Notifications/EmailOtp.php) (mail channel). |
| 31.11 | Policies | 12 in [app/Policies/](../app/Policies/). See Section 25.9. |
| 31.12 | Observers | **No `app/Observers/`** — model events via `boot()`/`booted()` closures inside models (Product slug, Category/Brand cascade-guards, Order updating, Feedback force-delete media cleanup, Activity, ChatbotFaq cache-bust). |
| 31.13 | Console Commands | 5 in [app/Console/Commands/](../app/Console/Commands/): `GenerateSitemap`, `ExpireUnpaidOrders`, `SendBookingReminders`, `TrimActivityLog`, `AutoResolveErrorLogs`. |
| 31.14 | Scheduled tasks | Defined in [routes/console.php](../routes/console.php) (Laravel 11+ style, no Kernel). See Appendix. |
| 31.15 | Helpers | [app/helpers.php](../app/helpers.php) (autoloaded via composer `files`) — `setting()` global. |
| 31.16 | Traits / Concerns | `app/Models/Concerns/HasSortableOrder`; `app/Filament/Concerns/NotifiesImportExportCompletion`; **`app/Livewire/Concerns/SetsSeo`** (per-page SEO meta) + **`NotifiesOwner`** (emails owner via OwnerAlertMail on key events). No `app/Traits/`. |
| 31.17 | Enums | **No `app/Enums/`** — statuses are string constants (`Booking::STATUSES`, `Order::statuses()`) + DB string columns. |
| — | Services | [app/Services/](../app/Services/): `Chat/MockDriver`, **`Booking/BookingService`** (slot generation, availability, closed-day/business-hours logic, lock-aware double-booking check), `EmailOtpService`, `ShippingCalculator`, `RefundCalculator`. |
| — | Support | [app/Support/](../app/Support/): **`Breadcrumbs`** (request-scoped observability breadcrumb trail, singleton, fed into log context by `ObservabilityProcessor`); **`SocialLogin`** (`enabled()` — lists OAuth providers with both keys set, drives the social-login buttons). |
| — | Logging | [app/Logging/](../app/Logging/): `CreateDatabaseLogger`, `DatabaseLogHandler` (writes `app_logs` + regression detection), `ObservabilityProcessor` (attaches trace id + breadcrumbs). |

---

# 32. Email & Notifications System

| # | Aspect | Detail |
|---|---|---|
| 32.1 | Templates | 10 mailables ([app/Mail/](../app/Mail/)): BookingConfirmation, BookingConfirmed, BookingCancelled, BookingReminder, OrderConfirmation, OrderShipped, OrderDelivered, OrderCancelled, OrderRefundProcessed, OwnerAlert. Blade views in [resources/views/mail/](../resources/views/mail/) + [components/mail/layout.blade.php](../resources/views/components/mail/layout.blade.php). OTP via [EmailOtp](../app/Notifications/EmailOtp.php). |
| 32.2 | Driver | Default `MAIL_MAILER=log` (dev). Production uses a **custom `gmail_api` transport** ([GmailApiTransport.php](../app/Mail/Transport/GmailApiTransport.php)) sending as the store Gmail via OAuth refresh token (access token cached 50 min). SMTP/SES/Postmark/Resend configs also present. |
| 32.3 | Queue | Mail is sent inline — every mailable call site uses `Mail::send()`, never `->queue()` — so `QUEUE_CONNECTION` doesn't apply to email regardless of its value (see 9 / 31.8 for the actual queue setup). |
| 32.4 | Notification channels | `mail` (OTP) + `database` (Filament bell). **No** broadcast (`BROADCAST_CONNECTION=log`). |
| — | Owner alerts | [NotifiesOwner](../app/Livewire/Concerns/NotifiesOwner.php) concern emails the store owner (`OwnerAlertMail`) on key customer events (e.g. new booking/order) with a heading, detail rows, and an action link. |
| 32.5 | Preview routes | **None** (no mailable preview route). |
| 32.6 | SMS | **Not implemented** — phone contact is WhatsApp/`tel:` only. |

---

# 33. Caching Strategy

| # | Aspect | Detail |
|---|---|---|
| 33.1 | Driver | `CACHE_STORE=database` (DB cache store); options for file/array/memcached in [config/cache.php](../config/cache.php). |
| 33.2 | `Cache::remember()` usage | `dashboard_stats` (60 s, StatsOverview); `setting_{key}` (1 h, Setting); `chatbot_faqs` (1 h), `chatbot_services` (10 min), `chatbot_brands` (10 min) in MockDriver; `gmail_api_access_token` (50 min). |
| 33.3 | View caching | `php artisan view:cache` on container boot. |
| 33.4 | Route caching | `php artisan route:cache` on boot. |
| 33.5 | Config caching | `php artisan config:cache` on boot ([docker-entrypoint.sh](../docker-entrypoint.sh)). |
| 33.6 | Invalidation | `Setting::setValue()` forgets `setting_{key}`; ChatbotFaq `booted()` busts `chatbot_faqs`; scheduler heartbeat uses `cache()->forever`. |
| 33.7 | TTL summary | Settings 3600 s · dashboard 60 s · chatbot FAQs 3600 s · chatbot services/brands 600 s · Gmail token 3000 s. |

---

# 34. Search Implementation

| # | Aspect | Detail |
|---|---|---|
| 34.1 | Engine | **Built-in SQL `LIKE`** (no Scout/Algolia/Meilisearch). |
| 34.2 | Searchable fields | `name`, `short_description`, `sku` ([ProductsPage.php](../app/Livewire/ProductsPage.php)) with proper `ESCAPE` + `addcslashes` to neutralize `%`/`_` wildcards. Filament admin tables additionally search name/brand/slug. |
| 34.3 | Across languages | Searches the base fields; translated descriptions (`description_ms/_zh`) are not separately indexed. |
| 34.4 | Autocomplete | **None** — live filtering on submit/typing via Livewire, no suggestion dropdown. |
| 34.5 | Search analytics | Not for products. Chatbot queries are logged (`chat_logs`). |
| 34.6 | Typo/fuzzy | **None** for product search (exact substring). Chatbot has stem/boundary tolerance. |

---

# 35. Error Handling & Error Pages

| # | Aspect | Detail |
|---|---|---|
| 35.1 | 404 | Custom [errors/404.blade.php](../resources/views/errors/404.blade.php) |
| 35.2 | 500 | Custom [errors/500.blade.php](../resources/views/errors/500.blade.php) |
| 35.3 | 503 maintenance | Custom [errors/503.blade.php](../resources/views/errors/503.blade.php) |
| — | 403 | Custom [errors/403.blade.php](../resources/views/errors/403.blade.php) (added 2026-07-02) — themed Access-Denied page for invoice/payment ownership rejections, with Home + Login actions |
| — | 419 / 429 | Custom CSRF-expired + rate-limited pages ([419](../resources/views/errors/419.blade.php), [429](../resources/views/errors/429.blade.php)); plus `unauthorized` |
| 35.4 | Validation errors | Livewire `@error`/error bags inline; Filament inline field errors |
| 35.5 | Exception logging & observability | **Self-hosted observability stack.** Log `stack` channel fans out to 3 channels ([config/logging.php](../config/logging.php)): `single` (file), `structured` (daily **JSON** at `storage/logs/structured.log`), and `database` (custom [CreateDatabaseLogger](../app/Logging/CreateDatabaseLogger.php) → [DatabaseLogHandler](../app/Logging/DatabaseLogHandler.php) → `app_logs`, with Sentry-style error grouping via an indexed, multibyte-safe `fingerprint` column: regression-reopen on write, a recurrence state machine for the admin "check for recurrence" action, and a silence-window `logs:auto-resolve` (all config-driven, survives `config:cache`). **Trace IDs:** [AssignTraceId](../app/Http/Middleware/AssignTraceId.php) reads/generates `X-Request-Id`, shares it + request metadata via Laravel **Context** so every log line is correlated, and echoes it back in the response header. [ObservabilityProcessor](../app/Logging/ObservabilityProcessor.php) attaches the trace id + breadcrumb trail to each entry. Custom exception render in [bootstrap/app.php](../bootstrap/app.php). |
| 35.6 | Monitoring tool | **None external** (no Sentry/Bugsnag/Flare) — in-app Logs resource + System Status page serve this role |
| 35.7 | Try/catch coverage | Chatbot AI/driver call, OTP send, Gmail transport, payment paths |

---

# 36. Performance Metrics & Optimization

| # | Aspect | Detail |
|---|---|---|
| 36.1 | Lighthouse / PageSpeed Insights | Audited with **Google PageSpeed Insights (Lighthouse)** on the production deploy — used as the diagnostic tool driving 36.11–36.12 below. Scores: **SEO 100 · Best Practices 100 · Accessibility 95 · Performance 72** (a feature-rich page carrying a ~5 MB hero video + 3D model + map). Core Web Vitals: CLS 0 and TBT 20 ms (good); FCP ~4.1 s / LCP ~5.0 s (the optimisation target) |
| 36.2 | Web Vitals | Not instrumented |
| 36.3 | Image format | WebP accepted on upload; conversions optimized; `webp`/optimizer binaries installed in Docker. AVIF not used |
| 36.4 | Responsive images | `srcset`/`sizes` **not** used; fixed-size `thumb`/`card` conversions instead |
| 36.5 | Critical CSS inline | Theme bootstrap + brand vars inlined in `<head>`; main CSS via Vite (not split critical CSS) |
| 36.6 | Resource hints | `preconnect` to Google Fonts; `preload` on main stylesheet; font `display=swap` |
| 36.7 | Code splitting | 5 Vite entry points ([vite.config.js](../vite.config.js)); the **~650 KB Three.js configurator chunk is dynamic-imported** only on open (`chunkSizeWarningLimit: 900` set for this deliberate split) so it never hits initial load; **11 `loading="lazy"` images** + **26 `wire:navigate`** SPA-style links verified across views |
| 36.8 | Compression | Handled by Render/Cloudflare edge (gzip/brotli), not app-level |
| 36.9 | Browser caching | Asset hashing by Vite; icon cache-busting by mtime |
| 36.10 | Query optimization | Dedicated index migrations; eager loading; cached settings/stats; `lockForUpdate` for concurrency. No slow-query log shipped |
| 36.11 | Load-path fixes (from the PageSpeed audit) | **Leaflet** (map library, ~46 KB CSS+JS) is loaded **only on the Contact page** via `@push` ([contact-page.blade.php](../resources/views/livewire/contact-page.blade.php)) instead of globally — every other page now skips it (it was render-blocking on all pages). The **LCP logo** carries `fetchpriority="high"` so the browser fetches it first. Covered by [AssetLoadingTest](../tests/Feature/AssetLoadingTest.php) |
| 36.12 | Known limits (from the PageSpeed audit) | Documented as accepted rather than changed: (a) static assets are served by `php artisan serve` (the Render Docker entrypoint), which sets no `Cache-Control` — so repeat visits re-download assets ("Cache TTL: None" in the audit); production would front this with nginx/caddy + cache headers. (b) The hero background video is ~5.2 MB uncompressed; further wins would come from transcoding it or a poster-image + lazy-load strategy. (c) Several third-party libs (AOS, lucide, model-viewer) still load from the unpkg CDN |

---

# 37. Cookie Consent & Privacy

| # | Aspect | Detail |
|---|---|---|
| 37.1 | Cookie consent banner | **Not implemented** |
| 37.2 | PDPA/GDPR | Privacy Policy page; minimal cookies (session, theme, locale); chatbot answers privacy/PDPA questions |
| 37.3 | Cookie categories | Only essential: the session cookie (name derived from `APP_NAME`, e.g. `win-win-car-audio-session` — not the Laravel default `laravel_session`; see [config/session.php](../config/session.php)), CSRF token, `app_theme`. Locale is stored **in the session**, not a separate cookie (see 46.5). No analytics/marketing cookies. |
| 37.4 | Data retention | Prunable tables (chat 90 d, carts 30 d, logs configurable); activity log trimmed to 5,000 |
| 37.5 | Data export (GDPR) | **Not implemented** |
| 37.6 | Account deletion | ✅ Self-service in [ProfilePage.php](../app/Livewire/ProfilePage.php) (OTP-confirmed for social-only accounts); soft-deletes user |

---

# 38. Analytics & Tracking

| # | Aspect | Detail |
|---|---|---|
| 38.1 | Google Analytics | **Not integrated** |
| 38.2 | Facebook Pixel | **Not integrated** |
| 38.3 | Custom event tracking | **None** (no client analytics events) |
| 38.4 | Heatmaps (Hotjar/Clarity) | **Not integrated** |
| 38.5 | Conversion tracking | **None** |

> No third-party analytics/tracking — a deliberate privacy-minimal posture. Internal signals only: `chat_logs`, `activity_log`, `app_logs`.

---

# 39. Customer Experience Enhancements

| # | Feature | Status |
|---|---|---|
| 39.1 | Recently viewed | ❌ Not implemented |
| 39.2 | Wishlist / save for later | ❌ Not implemented |
| 39.3 | Product comparison | ❌ Not implemented |
| 39.4 | Image gallery / lightbox | Single primary image per product (MediaLibrary `singleFile`); `images` JSON column exists but no multi-image lightbox |
| 39.5 | Image zoom on hover | ❌ Not implemented (hover scale only) |
| 39.6 | Product variants | ❌ No variant system (flat SKU per product) |
| 39.7 | Related products | ✅ Same-category, up to 4, on product detail page |
| 39.8 | "Customers also bought" | ❌ Not implemented |
| 39.9 | Quick view modal | ❌ Not implemented |
| 39.10 | Social share | Minimal (no per-product share buttons); WhatsApp deep links are the main share vector |
| 39.11 | Ratings/reviews | No per-product reviews — testimonials are global (`feedback`) |
| 39.12 | Stock alert / notify | ❌ Not implemented (backorder messaging instead) |
| — | **3D configurator** | ✅ The headline CX differentiator (Section 10) |

---

# 40. Testing & Code Quality

| # | Aspect | Detail |
|---|---|---|
| 40.1 | PHPUnit tests | **189 Feature tests + 4 Unit** (193 total, 637 assertions) in [tests/](../tests/): Auth flow, login lockout, booking (email/reminder/service/slot-availability/slot-race/confirm-guard/admin-edit), cart, guest-cart-claim, checkout, shipping, payment (flow/hardening/expiry-guard), Stripe (webhook/checkout-redirect/return-verification/method-mapper), order (cancellation/mark-paid-guard/admin/importer/tracker), invoice, products search, services, set-password, settings, shop-mode close, SEO structured data, asset loading (incl. page-loader i18n), localized pages, localization coverage, social login, staff bulk-delete auth, user admin auth, observability, error-log lifecycle, log resolved/resource, activity resource, system-status clear-cache |
| 40.2 | Browser / E2E tests | **None** — no Dusk or Playwright; feature behaviour is covered by the PHPUnit suite, and UI/JS is verified manually |
| 40.3 | Code style | **Laravel Pint** (`laravel/pint`) |
| 40.4 | Static analysis | **None** (no Larastan/PHPStan/Psalm) |
| 40.5 | Coverage report | Not configured |
| 40.6 | CI/CD pipeline | **No `.github/workflows`** — deploy is Render auto-deploy on push; tests run locally |

---

# 41. Documentation

| # | Aspect | Detail |
|---|---|---|
| 41.1 | README | [README.md](../README.md) (98 lines) — badges, about, feature list, TALL-stack note |
| 41.2 | Inline comments | **High density** — extensive "why" comments throughout (security rationale, race-condition notes, config explanations) |
| 41.3 | PHPDoc | Present on models/services/methods (e.g. User casts security note) |
| 41.4 | API docs | N/A — no public API |
| 41.5 | Setup guide | composer `setup` script + README; [.env.example](../.env.example) documented |
| 41.6 | Deployment guide | Captured in Dockerfile/entrypoint/.env.example comments + this audit (Section 22) |
| 41.7 | Contributing guide | ❌ None |
| 41.8 | Changelog | ❌ None (git history serves as the log) |
| — | This audit | [docs/SYSTEM_AUDIT.md](SYSTEM_AUDIT.md) |

---

# 42. Site Navigation Details

| # | Aspect | Detail |
|---|---|---|
| 42.1 | Main nav | Home, Products, Services, Booking, About, Contact (+ cart/account/login conditionally) |
| 42.2 | Mobile menu | Alpine hamburger toggle in [layouts/app.blade.php](../resources/views/layouts/app.blade.php) |
| 42.3 | Breadcrumbs | ✅ UI breadcrumb on product detail page (`aria-label="Breadcrumb"` nav); Filament admin: built-in resource breadcrumbs. *(Note: [app/Support/Breadcrumbs.php](../app/Support/Breadcrumbs.php) is unrelated — it's an observability/log breadcrumb trail, not UI.)* |
| 42.4 | Footer nav | `<footer role="contentinfo">` (`bg-brand-black`) with **4 regions**: (1) brand blurb, (2) **Quick Links** — Home/Products/Services/Book Appointment/About/Contact, (3) **Contact Us** — Google Maps link/`tel:`/`mailto:`/WhatsApp/Facebook, (4) bottom bar — copyright + **legal links** (Privacy Policy, Terms of Service, Cancellation & Refund Policy, FAQ). Auth pages hide it. |
| 42.5 | Sticky header | `<nav>` sticky `top-0 z-50`, white/dark bg + shadow + bottom border; holds logo, main links, language dropdown, theme toggle, account dropdown, cart drawer trigger; mobile hamburger; set-password banner sits above it when applicable |
| 42.6 | Back-to-top | ✅ Storefront ([layouts/app.blade.php](../resources/views/layouts/app.blade.php)) + Filament admin ([scroll-to-top.blade.php](../resources/views/filament/scroll-to-top.blade.php)) |
| 42.7 | Search bar | On Products page (not global nav) |
| 42.8 | Mega menu | ❌ None |

---

# 43. Conversion / CTA Strategy

| # | Aspect | Detail |
|---|---|---|
| 43.1 | Primary CTAs | Browse Products, Book Appointment, WhatsApp Us, 3D Configurator, Enquire Configuration |
| 43.2 | Placement | Hero (above fold), after each homepage section, sticky WhatsApp affordance, footer |
| 43.3 | Trust signals | 21 customer testimonials, 8 brand logos marquee, business address/hours/map |
| 43.4 | Urgency cues | Stock-based ("low stock" warnings admin-side; backorder messaging); 15-min payment countdown on the payment page |
| 43.5 | Social proof | Testimonials section + brand partners marquee |

---

# 44. Content Management Strategy

| # | Aspect | Admin-manageable? |
|---|---|---|
| 44.1 | Homepage sections | 8 sections in [home-page.blade.php](../resources/views/livewire/home-page.blade.php): **Hero** (video) · **Browse by Category** · **Featured Products** · **Why Choose Win Win** (4 cards) · **Showcase** (about/3D promo) · **Testimonials slider** · **Brands marquee** · **CTA banner**. Data (products/categories/brands/testimonials) is DB-driven & **editable**; section layout/copy is hardcoded Blade |
| 44.2 | Hero video/banner | ❌ Hardcoded asset (no admin hero manager) |
| 44.3 | Featured products | ✅ Admin toggles `is_featured` per product (manual selection) |
| 44.4 | Testimonials | ✅ Admin-created/edited via Testimonials resource (no public submission form → admin curates). Seeded from **real Google reviews** (Local Guide tags, review counts) — [FeedbackSeeder.php](../database/seeders/FeedbackSeeder.php) |
| 44.5 | Brand logos | ✅ Admin Brands resource (add/sort/show-hide) |
| 44.6 | "Why Choose Us" cards | ❌ Hardcoded in Blade |

---

# 45. Database & Migration Details

| # | Aspect | Detail |
|---|---|---|
| 45.1 | Migration count | **67** files in [database/migrations/](../database/migrations/) |
| 45.2 | Seeders | DatabaseSeeder + CarModelSeeder, ChatbotFaqSeeder (+ `chatbot_faqs.json`), FaqSeeder, FeedbackSeeder, ProductSeeder |
| 45.3 | Factories | Only [UserFactory](../database/factories/UserFactory.php) |
| 45.4 | Soft deletes | `users`, `orders`, `bookings`, `feedback`, `contacts` |
| 45.5 | Timestamps | Standard `created_at`/`updated_at`; `deleted_at` on soft-deleted; lifecycle timestamps on orders (paid/shipped/delivered/cancelled) |
| 45.6 | IDs | Auto-increment integers (bigint); `settings` uses string PK (`key`); `sessions` string PK; **no UUIDs** |
| 45.7 | FK constraints | `nullOnDelete` (products→categories, orders→users), `cascadeOnDelete` (social_accounts→users), plain constrained pivots |
| 45.8 | JSON columns | `products.images/specs/compatible_vehicles`, `app_logs.context`, `activity_log.properties/attribute_changes` (cast to array) |
| 45.9 | Translatable columns | **Separate columns** (`description`, `description_ms`, `description_zh`) — not spatie/translatable; resolved by accessor |
| 45.10 | Indexes | `slug`/`sku` unique; sessions `last_activity`+`user_id`; dedicated perf-index migrations for core tables, orders, app_logs |

---

# 46. Routes Organization

| # | Aspect | Detail |
|---|---|---|
| 46.1 | web.php route count | ~30 route declarations ([routes/web.php](../routes/web.php)) |
| 46.2 | Groups & middleware | `auth`, `auth:web,admin`, `auth:admin`, `ShoppingEnabled`, `throttle:*` groups |
| 46.3 | Named routes | ✅ Every route named (`home`, `products`, `product.show`, `cart`, `checkout`, `invoice.show`, …) |
| 46.4 | Route caching | Enabled at boot |
| 46.5 | Locale routing | **Session-based** (`/lang/{locale}` sets session) — **not** URL-prefixed |
| 46.6 | API routes | **No `api.php`** — no REST API |
| 46.7 | Sitemap route | `/sitemap.xml` serves generated file |

---

# 47. Localization Files

| # | Aspect | Detail |
|---|---|---|
| 47.1 | Structure | JSON-based: [lang/ms.json](../lang/ms.json), [lang/zh.json](../lang/zh.json); English is the source key (no `lang/en.json`). PHP files for framework messages: [lang/zh/validation.php](../lang/zh/validation.php) + [lang/ms/validation.php](../lang/ms/validation.php) (added 2026-07-02 — full rule set + friendly attribute names for every form field) |
| 47.2 | Keys | Flat key=English-string → translation maps |
| 47.3 | Completeness | Enforced by [LocalizationCoverageTest](../tests/Feature/LocalizationCoverageTest.php) — guards MS/ZH parity with used `__()` keys; end-to-end rendering guarded by [LocalizedPagesTest](../tests/Feature/LocalizedPagesTest.php) |
| 47.4 | `__()`/`trans()` | Used throughout Blade + components for all user-facing strings; DB-driven service & category names pass through `__()` with JSON entries |
| 47.5 | Pluralization | Standard Laravel; `:count`/`:seconds` placeholders used |
| 47.6 | Date/number per locale | MYR money formatted `locale: ms_MY`; dates localized via Carbon — `LocaleUpdated` listener in [AppServiceProvider.php](../app/Providers/AppServiceProvider.php) keeps Carbon's locale in lockstep, and customer-facing views use `translatedFormat()` (added 2026-07-02) |
| 47.7 | Product content translations | Per-language columns `name_ms/_zh`, `short_description_ms/_zh`, `description_ms/_zh` on `products` + `translated_*` accessors with English fallback ([Product.php](../app/Models/Product.php)); entered via Filament product form (added 2026-07-02) |

---

# 48. Final QA Checklist

| # | Check | Result |
|---|---|---|
| 48.1 | `console.log` in JS | ⚠️ One left: [configurator.js:985](../resources/js/configurator.js) (`'Mapped Car Parts:'`) — debug aid; remove for production polish |
| 48.2 | `dd()`/`dump()`/`var_dump()` in PHP | ✅ None found in `app/` |
| 48.3 | `.env.example` complete | ✅ Documents APP/DB(mysql+TiDB)/MAIL/GOOGLE/CRON_SECRET/DEFAULT_ADMIN; some optional store/OAuth keys come from `config/services.php` defaults |
| 48.4 | `.gitignore` sane | ✅ Ignores `.env`, `.env.*`, `/vendor`, `/node_modules`, logs, `*.key`; large raw `.glb` originals ignored |
| 48.5 | Lockfiles committed | ✅ `composer.lock` + `package-lock.json` both tracked |
| 48.6 | No secrets in public/ | ✅ No production `.env` or secrets committed under `public/` |
| 48.7 | Storage symlink | ✅ `php artisan storage:link --force` on every container boot; media committed to git (Render has no persistent disk) |
| 48.8 | Render config file | No `render.yaml`/`Procfile` — Docker-based deploy configured via the Render dashboard ([Dockerfile](../Dockerfile) + [docker-entrypoint.sh](../docker-entrypoint.sh)) |
| — | Seeder safety | ✅ DatabaseSeeder refuses to run without explicit `DEFAULT_ADMIN_*` (no public default credentials) |

---

# 49. Deep Security & UX Audit — 2 Jul 2026

> Full-codebase pass over authentication, authorization, payment, injection/XSS surface, config posture, and UX gaps. Method: route/middleware review, pattern greps (`{!! !!}`, raw SQL, mass assignment, `env()` leaks), file-by-file reads of every money/auth/lookup flow, plus git-history secret scan (§4.13 re-confirmed).

## 49.1 Findings & resolutions

| # | Severity | Finding | Resolution |
|---|---|---|---|
| 1 | Medium | **Order/booking lookup enumeration** — trackers returned different messages for "no such number" vs "wrong email"; with sequential `ORD-/BK-` numbers this let anyone probe which numbers exist (business-volume leak) | **Fixed** — unified not-found message in both trackers ([OrderTracker.php](../app/Livewire/OrderTracker.php), [BookingTracker.php](../app/Livewire/BookingTracker.php)), search + cancel paths |
| 2 | Medium (UX-visible) | **Login "attempts remaining" counter appeared stuck at 4** — Livewire persists the error bag across requests; `addError()` appends while `@error` renders `first()`, so every retry displayed the first stale message; separately, `lockoutSecondsFor()` matched tiers with `>=` so every failure past the 5th re-locked instantly (countdown was dead code) | **Fixed** — error-bag reset at the start of every reporting action across UserLogin, ForgotPassword, ProfilePage (11 actions, field-scoped), BookingForm, ContactPage, CheckoutPage; tier logic fires only at 5/10/15/20/25 boundaries; covered by [LoginLockoutTest](../tests/Feature/LoginLockoutTest.php) |
| 3 | Low | **`trustProxies(at:'*')` + all X-Forwarded headers** — spoofable client IPs would undermine per-IP rate limits if ever deployed without a trusted edge; X-Forwarded-Host spoofing could poison generated URLs | **Hardened** — `TRUSTED_PROXIES` env override (default `*` for Render), X-Forwarded-**Host** no longer trusted ([bootstrap/app.php](../bootstrap/app.php)) |
| 4 | Low | **No custom 403 page** — invoice/payment ownership `abort(403)` fell back to Laravel's unthemed default | **Fixed** — [errors/403.blade.php](../resources/views/errors/403.blade.php) matching the 404/500 design, localized, with Home + Login actions |
| 5 | Low | Sequential order/booking numbers; CSP `unsafe-inline`/`unsafe-eval`; English-only transactional mails | **Accepted risks**, documented in §9 with rationale |
| 6 | Medium (admin-only) | **Error-log recurrence logic** — "Check for recurrence" anchored on the group's *newest* row, so "still recurring" was unreachable and the action always resolved everything; grouping compared PHP byte-based `substr()` to SQL character-based `SUBSTR()`, silently failing on any multibyte message; every error write ran an unindexed `SUBSTR()` scan; `env()` in the auto-resolve command/pruner is dead once config is cached | **Fixed (follow-up)** — indexed `fingerprint` column (mb-safe, backfilled), recurrence state machine on [AppLog](../app/Models/AppLog.php) (`active` <1 h / `recurred` / `quiet`) anchored on the checked entry, set-based `logs:auto-resolve`, `config('logging.db_log.*')` instead of `env()`; the action is also hidden on non-error rows (its error-only grouping would otherwise report "0 resolved" and leave the row open); covered by [ErrorLogLifecycleTest](../tests/Feature/ErrorLogLifecycleTest.php) |
| 7 | Medium | **App timezone was UTC for a Malaysian store** — every "today"/"past" boundary (booking calendar day edges, the submit-time `isPast()` guard) and all displayed record timestamps ran 8 h behind local reality; picking "today" also listed slots whose time had already gone, rejecting the customer only at the final submit step | **Fixed** — `app.timezone` → `Asia/Kuala_Lumpur` (env-overridable via `APP_TIMEZONE`; rows written under UTC display 8 h late, new rows correct) and [BookingService::getAvailableSlots](../app/Services/Booking/BookingService.php) now filters out slots not in the future; covered by [BookingSlotAvailabilityTest](../tests/Feature/BookingSlotAvailabilityTest.php) |
| 8 | Low (narrow race) | **Admin "Mark Paid" vs auto-expiry race** — the expiry scheduler cancels unpaid orders while leaving `payment_status` 'pending', and markPaid's locked re-check only tested that column, so a click racing a cancellation could produce a cancelled-but-paid order (stock already restocked) and email the customer a confirmation | **Fixed** — locked re-check also requires `status != 'cancelled'`, mirroring `PaymentPage::pay()`'s atomic guard; covered by [OrderMarkPaidGuardTest](../tests/Feature/OrderMarkPaidGuardTest.php) |
| 9 | Low | Three consistency gaps: **admin CreateBooking missed the slot cache lock** that BookingForm/EditBooking both hold (phantom-read double-booking window on TiDB, which takes no gap locks); **admin Confirm blindly updated the row**, able to resurrect a just-cancelled booking (single + bulk); **product search only matched English columns**, so a MS/ZH visitor couldn't find products by the translated name their own cards display | **Fixed** — same `booking-slot:` lock in CreateBooking; Confirm is an atomic `pending→confirmed` claim with an "already changed" notice; search spans `name_ms/name_zh/short_description_ms/_zh`; covered by [BookingConfirmGuardTest](../tests/Feature/BookingConfirmGuardTest.php) + [ProductsPageSearchTest](../tests/Feature/ProductsPageSearchTest.php) |
| 10 | Low (audit-trail) | **`PaymentPage::expireOrder()` checked only "awaiting payment", not the actual deadline** — it's a public Livewire action, so a customer could invoke it from the browser console before the 15-minute window lapsed and stamp their own cancellation as `cancelled_by='system'` / "payment not completed", polluting the audit trail (no financial impact: only their own unpaid order, which they may cancel anyway) | **Fixed** — the locked re-check also requires `isPaymentExpired()`; the on-page timer is server-seeded so legitimate calls self-heal; covered by [PaymentExpiryGuardTest](../tests/Feature/PaymentExpiryGuardTest.php) |
| 11 | Medium | **Admin "Clear System Cache" ran `Cache::flush()`** — the database cache store also holds all security state (login-failure counts, per-email lockouts, IP blocks, live OTP codes + attempt caps, every RateLimiter key, chatbot abuse blocks), so one admin click would unlock an in-progress brute-forcer, invalidate codes for users mid-signup/reset, and reset every rate limit; it also ran `config:/route:/view:clear` in production (clear-without-rebuild → slower until next deploy, and none hold the DB content the button targets) | **Fixed** — [SystemStatus::clearCache](../app/Filament/Pages/SystemStatus.php) now forgets only the content keys (`setting_*`, `dashboard_stats`, `chatbot_faqs/services/brands`, `gmail_api_access_token`); no `flush()`, no artisan clears; covered by [SystemStatusClearCacheTest](../tests/Feature/SystemStatusClearCacheTest.php) |
| 12 | UX (design) | **Shop-mode switch left half-finished customers stranded** — turning online shopping off gated `/pay` but left unpaid orders showing a dead-end "Pay now", and gave customers no signal shopping had paused (a paid customer had no reassurance their order was safe) | **Fixed (feature)** — closing shopping now cancels + restocks + emails all **unpaid** orders while never touching **paid** ones ([ShopModeService](../app/Services/ShopModeService.php) via the `Setting` updated event); an admin-controlled, responsive, dismissible **site-wide announcement bar** (`SITE_ANNOUNCEMENT_*`) signals the pause; paid orders show a reassurance line and the dead-end "Pay now" is hidden. Full detail in **§20.5–20.6**; covered by [ShopModeCloseTest](../tests/Feature/ShopModeCloseTest.php) |
| 13 | Low (SEO, post-launch) | **Submitted sitemap rejected by Google** — Search Console flagged all 22 URLs as "URL not allowed" because they read `http://capstone.test/…`: `sitemap:generate` runs from the CLI with no request, so `route()` fell back to `APP_URL`, which is the local Herd `.test` domain in development | **Fixed** — [GenerateSitemap](../app/Console/Commands/GenerateSitemap.php) forces `URL::forceRootUrl(config('services.store.url'))` + `forceScheme('https')` so URLs are always the production host regardless of where generated; covered by [SeoStructuredDataTest](../tests/Feature/SeoStructuredDataTest.php) |
| 14 | Low (a11y) | **Malformed accessibility tree** — Lighthouse's Agentic-Browsing audit flagged the desktop nav's `role="list"` container whose direct children are `<a>` links, not `role="listitem"` (a list role requires listitem children); adding the role to the links would have overridden their "link" role | **Fixed** — dropped the redundant `role="list"` (the links live in `<nav>`, which already carries the semantics); footer `<ul>` and product breadcrumb `<ol>` already pair correctly |

## 49.2 Verified-clean areas (no action needed)

- **Payment** ([PaymentPage.php](../app/Livewire/PaymentPage.php)): cache lock + pessimistic row lock + atomic conditional flip (single-winner); server-side price computation; whitelisted payment methods; `#[Locked]` order property.
- **IDOR**: invoice, payment page, account pages all enforce `user_id` ownership or admin/staff role.
- **Injection**: every `whereRaw` parameter-bound; product search escapes LIKE wildcards (`ESCAPE '!'`).
- **XSS**: all `{!! !!}` sinks are trusted SEO generators, hardcoded SVG icons, or pre-escaped (`nl2br(e(...))`, chatbot `e()` before phone-linkification).
- **Privilege escalation**: `role` not mass-assignable; profile update uses field whitelist (email immutable); admin panel double-guarded (`Authenticate` + `AdminMiddleware` role check).
- **Chatbot**: input capped + `strip_tags`, injection/moderation classifiers, burst + hourly rate limits, abuse cooldown, `#[Locked]` state, MockDriver (no external LLM key to leak).
- **Secrets**: `.env*` ignored; git history clean; cron endpoint `hash_equals`-gated; seeder refuses default credentials.

## 49.3 Remaining UX notes (minor, unfixed by choice)

- Language switch is a full-page redirect (correctly open-redirect-guarded) rather than soft navigation.
- The custom 429 page mostly applies to route-level throttles; component-level limits intentionally show inline messages instead.
- Guest bookings without an email rely on the on-screen reference; consider a "screenshot this" nudge on the success card.
