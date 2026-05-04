<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="380" alt="Laravel Logo">

# Win Win Car Studio

**Malaysian Car Accessories E-Commerce & Workshop Booking Platform**

<br>

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4.2-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5-F59E0B?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-Dev-003B57?style=for-the-badge&logo=sqlite&logoColor=white)

</div>

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Multi-Language Support](#multi-language-support)
- [Admin Panel](#admin-panel)
- [AI Chatbot](#ai-chatbot)
- [Security](#security)
- [Routes](#routes)
- [Production Deployment](#production-deployment)
- [Windows / Herd Notes](#windows--herd-notes)

---

## Overview

**Win Win Car Studio** is a full-stack B2C e-commerce platform built for a Malaysian car accessories showroom, using the **TALL stack** (Tailwind + Alpine + Livewire + Laravel).

The customer-facing storefront supports three languages (English, Bahasa Melayu, Chinese) and lets shoppers browse products, check vehicle compatibility, place orders, book workshop appointments, and chat with an AI-powered mechanic assistant. The store owner manages the entire operation through a Filament 5 admin panel covering sales, scheduling, inventory, staff, and system settings.

---

## Features

### Customer Storefront

| Section | Description |
|---------|-------------|
| **Home** | Hero banner, featured products, category grid, statistics, testimonials, CTAs |
| **Product Catalog** | Live search, category filtering, product detail pages with image gallery |
| **Cart & Checkout** | Add to cart, quantity management, secure checkout flow |
| **Order Tracking** | Logged-in users view order history; guests look up status by order number |
| **Workshop Services** | Browse bookable services with pricing and duration |
| **Booking** | Real-time slot selection, appointment creation, management via token link |
| **Booking Tracker** | Look up appointment status by mobile number |
| **Compatibility Checker** | Brand → Model → Year three-level selector to find matching accessories |
| **AI Mechanic Chatbot** | Floating assistant powered by Anthropic Claude API |
| **Gallery** | Filterable masonry photo wall showcasing completed car modifications |
| **User Profile** | Registration, login, profile editing, saved addresses |
| **Contact** | Spam-protected contact form with honeypot and rate limiting |
| **Static Pages** | About Us, FAQ, Privacy Policy, Terms of Service |

**Cross-cutting capabilities:**

- **Multi-language** — Switch between EN / BM / ZH, preference saved in session
- **Dark / Light mode** — `localStorage` + `html.dark` class, Tailwind CDN `darkMode: 'class'`
- **Fully responsive** — Mobile, tablet, and desktop optimised
- **Accessibility** — ARIA labels, skip-to-content link, `focus-visible` styles, `prefers-reduced-motion`
- **SEO** — Per-page metadata, Open Graph tags, auto-generated XML sitemap

### Admin Panel (`/admin`)

Powered by **Filament 5**:

| Resource | Capabilities |
|----------|-------------|
| **Dashboard** | KPI cards (revenue, pending bookings, unread enquiries, orders) + charts |
| **Orders** | View and update order status, fulfilment, shipping, payment method |
| **Products** | Full CRUD, Spatie MediaLibrary image uploads, vehicle compatibility, stock |
| **Categories** | Hierarchical category management |
| **Brands** | Vehicle and product brand CRUD |
| **Services** | Workshop service pricing, duration, and availability scheduling |
| **Bookings** | Review, schedule, complete, or cancel workshop appointments |
| **Customers** | View customer profiles and order history |
| **Contacts** | Handle enquiry inbox, mark read/unread |
| **Feedback** | Moderate and publish customer testimonials |
| **Gallery** | Manage showcase albums; Spatie MediaLibrary auto-generates thumbnails |
| **Users** | Staff account management and role assignment |
| **Activity Log** | Spatie ActivityLog audit trail for all model changes |
| **Settings** | Global key-value system settings with caching |

---

## Tech Stack

| Category | Technology |
|----------|------------|
| Backend Framework | Laravel 13 (PHP 8.3+) |
| Reactive UI | Livewire 4.2 |
| Admin Panel | Filament 5 |
| CSS Framework | Tailwind CSS (CDN — no build step required) |
| Database | SQLite (dev) / MySQL (production) |
| Media Management | Spatie Laravel MediaLibrary 11 + Intervention Image 4 |
| Audit Logging | Spatie Laravel ActivityLog |
| Sitemap | Spatie Laravel Sitemap |
| SEO | Artesaos SEOTools |
| AI Integration | Anthropic Claude API (`claude-haiku-4-5`) |
| Spam Protection | Spatie Laravel Honeypot |
| Build Tool | Vite 8 |
| Dev Toolbar | Barryvdh Laravel Debugbar |

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                  Customer Storefront                     │
│   22 Livewire components · 3 languages · Dark mode      │
│   AI Chatbot · Compatibility Checker · SEO              │
└──────────────────────┬──────────────────────────────────┘
                       │  Laravel Router / Middleware
┌──────────────────────▼──────────────────────────────────┐
│                  Application Core (app/)                 │
│   17 Models · 5 Middleware · 5 Policies                 │
│   BookingService · AiService (Mock / Ollama / Claude)   │
└────────────┬──────────────────────┬─────────────────────┘
             │                      │
┌────────────▼──────────┐  ┌────────▼──────────────────┐
│   Admin Panel (/admin) │  │        Database Layer      │
│   Filament 5           │  │  SQLite/MySQL · 36 Migrations │
│   13 Resources         │  │  Media Library · Queue Jobs│
│   6 Dashboard Widgets  │  └───────────────────────────┘
└───────────────────────┘
```

**Authentication:**
- Customers — `web` guard (`users` table, `customer` role)
- Admins — `admin` guard (`users` table, `owner / admin / staff` roles), Filament login page

---

## Installation

### Requirements

- PHP >= 8.3 with extensions: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`
- Composer >= 2
- Node.js (optional — Tailwind is loaded via CDN, no local build needed)

### Steps

**1. Clone and install dependencies**
```bash
git clone <repo-url> winwin
cd winwin
composer install
```

**2. Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

**3. Configure `.env`**
```env
APP_NAME="Win Win Car Studio"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

# Optional: enable AI chatbot (hidden if not set)
ANTHROPIC_API_KEY=sk-ant-...
AI_DRIVER=claude        # Options: claude | ollama | mock

# Store contact info
STORE_NAME="Win Win Car Studio"
STORE_PHONE_DISPLAY="+60 12-345 6789"
STORE_EMAIL=info@winwincarstudio.com
```

**4. Run migrations and seeders**
```bash
php artisan migrate --seed
```

> Seeds 31 Malaysian car models, sample products, testimonials, and a default admin account.
>
> Default admin login: `admin@example.com` / `password`

**5. Link storage and generate sitemap**
```bash
php artisan storage:link
php artisan sitemap:generate
```

**6. Start the development server**
```bash
# Web server only
php artisan serve

# Web server + queue worker (recommended for image uploads)
composer run dev
```

- Storefront: `http://localhost:8000`
- Admin panel: `http://localhost:8000/admin`

### Image Processing Queue

Product, service, and gallery images are processed asynchronously by Spatie MediaLibrary (auto-generates thumbnails). Make sure the queue worker is running:

```bash
php artisan queue:listen
```

Or switch to synchronous processing for quick local testing:
```env
QUEUE_CONNECTION=sync
```

---

## Project Structure

```
capstone/
├── app/
│   ├── Console/Commands/       # GenerateSitemap Artisan command
│   ├── Contracts/              # AiServiceInterface abstraction
│   ├── Filament/
│   │   ├── Pages/              # Dashboard, Auth/Login
│   │   ├── Resources/          # 13 CRUD resources (Forms / Tables / Pages)
│   │   └── Widgets/            # 6 dashboard widgets
│   ├── Http/Middleware/        # AdminMiddleware, SecurityHeaders, SetLocale, ShoppingEnabled
│   ├── Livewire/               # 22 Livewire page components
│   │   ├── Auth/               # UserLogin
│   │   └── Concerns/           # SetsSeo trait
│   ├── Mail/                   # OrderConfirmationMail
│   ├── Models/                 # 17 Eloquent models
│   ├── Policies/               # 5 authorisation policies
│   ├── Providers/              # AppServiceProvider, AiServiceProvider, AdminPanelProvider
│   └── Services/
│       ├── Ai/                 # MockDriver, OllamaDriver
│       └── Booking/            # BookingService (availability logic)
├── config/                     # 17 configuration files
├── database/
│   ├── factories/
│   ├── migrations/             # 36 migrations
│   └── seeders/                # DatabaseSeeder, CarModelSeeder
├── lang/
│   ├── ms.json                 # Bahasa Melayu translations
│   └── zh.json                 # Simplified Chinese translations
├── public/
│   └── sitemap.xml             # Auto-generated
├── resources/
│   └── views/
│       ├── components/         # chatbot, compatibility-checker, empty-state, page-loader
│       ├── errors/             # 404, 419, 500, unauthorized
│       ├── filament/           # theme-toggle, scroll-to-top
│       ├── layouts/            # app.blade.php, admin.blade.php
│       ├── livewire/           # 21 Blade view files
│       └── mail/               # order-confirmation.blade.php
└── routes/
    └── web.php                 # 24 route definitions
```

---

## Database Schema

36 migrations total. Core tables:

| Table | Description |
|-------|-------------|
| `users` | Users with roles (`owner / admin / staff / customer`), address and phone fields |
| `products` | Products with slug, pricing, stock, specs, and multi-language descriptions |
| `categories` | Hierarchical product categories |
| `brands` | Vehicle and product brands |
| `car_models` | 31 seeded Malaysian car models (Perodua, Proton, Toyota, Honda, etc.) |
| `product_compatibilities` | Product-to-vehicle compatibility mapping |
| `orders` | Orders with status, payment method, shipping info |
| `order_items` | Order line items with product name snapshot |
| `cart_items` | User shopping cart |
| `services` | Bookable workshop services with pricing and scheduling |
| `bookings` | Appointments with token, status, and admin notes |
| `contacts` | Contact form submissions |
| `feedback` | Customer testimonials with media |
| `gallery_items` | Showcase gallery images |
| `settings` | Global key-value system settings (cached) |
| `media` | Spatie MediaLibrary file records |
| `activity_log` | Spatie ActivityLog audit trail |
| `ai_logs` | AI chatbot interaction history |

---

## Multi-Language Support

| Language | Code | Source |
|----------|------|--------|
| English (default) | `en` | Inline `__('...')` in PHP |
| Bahasa Melayu | `ms` | `lang/ms.json` |
| Simplified Chinese | `zh` | `lang/zh.json` |

**Switching flow:**
```
User clicks language button
  → GET /lang/{locale}
  → SetLocale middleware reads session('locale')
  → App::setLocale() applied globally
  → Redirect back to previous page
```

Translations cover navigation, feature descriptions, product terms, form labels, and error messages (100+ keys per language).

---

## Admin Panel

Access: `http://localhost:8000/admin`

**Role permissions:**

| Role | Access |
|------|--------|
| `owner` | Full system access |
| `admin` | Business data management (orders, products, bookings) |
| `staff` | Limited read/update access |
| `customer` | Storefront only — no admin access |

**Dashboard widgets:**
- `StatsOverview` — Revenue, pending bookings, unread enquiries, order count
- `RevenueChart` — Revenue trend line chart
- `TopProductsChart` — Best-selling products ranking
- `CategoryDistributionChart` — Sales by product category
- `RecentOrdersWidget` — Latest orders list
- `RecentActivityWidget` — System activity stream

---

## AI Chatbot

Three operating modes:

| Mode | Config | Description |
|------|--------|-------------|
| `claude` | Requires `ANTHROPIC_API_KEY` | Uses Anthropic `claude-haiku-4-5` |
| `ollama` | Requires local Ollama running | Uses a local LLM (`OLLAMA_HOST`) |
| `mock` | No API key needed | Returns preset responses for development |

```env
AI_DRIVER=mock      # Development
AI_DRIVER=ollama    # Local LLM
AI_DRIVER=claude    # Production
```

If `ANTHROPIC_API_KEY` is not set, the chat button is hidden and replaced with a standard contact prompt. All interactions are logged to the `ai_logs` table.

---

## Security

| Mechanism | Implementation |
|-----------|---------------|
| HTTP security headers | `SecurityHeaders` middleware (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, etc.) |
| Honeypot spam protection | Hidden `honeypot` field in `ContactPage` — bots fill it, submission is silently discarded |
| Rate limiting | Contact form: max 3 submissions per IP per 5 minutes via `RateLimiter` |
| Input sanitisation | All user text fields processed with `strip_tags()` before persistence |
| CSRF protection | Laravel built-in CSRF token verification on all forms |
| Role-based access control | `Policy` classes + `AdminMiddleware` guard the admin panel |
| Session security | Database session driver, 120-minute lifetime |
| Open redirect prevention | `/lang/{locale}` validates locale against a whitelist before redirecting |

---

## Routes

| Path | Component | Notes |
|------|-----------|-------|
| `GET /` | `HomePage` | Public |
| `GET /products` | `ProductsPage` | Public |
| `GET /products/{slug}` | `ProductDetail` | Public |
| `GET /services` | `ServicesPage` | Public |
| `GET /booking` | `BookingForm` | Public |
| `GET /booking/track` | `BookingTracker` | Public |
| `GET /booking/{token}` | `BookingManage` | Public |
| `GET /gallery` | `GalleryPage` | Public |
| `GET /track-order` | `OrderTracker` | Public |
| `GET /about` | `AboutPage` | Public |
| `GET /contact` | `ContactPage` | Public |
| `GET /faq` | `FaqPage` | Public |
| `GET /privacy-policy` | `PrivacyPolicyPage` | Public |
| `GET /terms-of-service` | `TermsOfServicePage` | Public |
| `GET /sitemap.xml` | — | Serves `public/sitemap.xml` |
| `GET /cart` | `CartPage` | Auth + ShoppingEnabled |
| `GET /checkout` | `CheckoutPage` | Auth + ShoppingEnabled |
| `GET /my-orders` | `MyOrdersPage` | Auth required |
| `GET /profile` | `ProfilePage` | Auth required |
| `GET /login` | `UserLogin` | Guest only |
| `GET /lang/{locale}` | — | Switches language (en / ms / zh) |
| `* /admin` | Filament | Admin role required |

---

## Production Deployment

**1. Set production environment variables**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=winwin
DB_USERNAME=your_user
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_DRIVER=file
```

**2. Run production build**
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan sitemap:generate
php artisan optimize
```

**3. Configure cron (scheduler)**
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**4. Configure queue worker (Supervisor)**
```ini
[program:winwin-worker]
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
```

---

## Windows / Herd Notes

**Session drops immediately after login:**
```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

**SQLite write permission error (`rename(): Access is denied`):**
```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
php artisan view:clear
```

---

## License

Built as a capstone project for academic demonstration purposes. All sample data and images are for demonstration only.
