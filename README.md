<div align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>

  <h1 align="center">Win Win Car Studio</h1>

  <p align="center">
    A premium full-stack web platform for a modern car accessories showroom and workshop.
  </p>

  <p align="center">
    <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
    <img src="https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 4">
    <img src="https://img.shields.io/badge/Filament-5-F59E0B?style=for-the-badge" alt="Filament 5">
    <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
    <img src="https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
    <img src="https://img.shields.io/badge/SQLite-dev-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  </p>
</div>

---

## 📖 Overview

Built with the modern TALL stack (**Laravel 13**, **Livewire 4**, **Filament 5**), **Win Win Car Studio** is a robust e-commerce and booking management platform explicitly tailored for automotive accessory studios. 

It provides customers with a localized (English, Bahasa Melayu, Chinese), mobile-first storefront to browse products, check vehicle compatibility, book installations, track orders natively, and even interact with an AI-powered mechanic chatbot. The store owner manages the entire sales and scheduling lifecycle via a powerful administration panel.

---

## ✨ Features

### 🏪 Customer Storefront

| Section          | Description                                                                                          |
| ---------------- | ---------------------------------------------------------------------------------------------------- |
| **Home**             | Hero banner, featured products, category grid, statistics, testimonials, CTA.                       |
| **Shop & Cart**      | Full product catalog, live search, categories, filtering, add to cart, and a secure checkout flow.  |
| **Orders & Tracking**| Customers can view past orders ("My Orders") and look up e-commerce statuses via order number tracker. |
| **Services & Booking**| View bookable services, pricing, and duration. Book active time slots with real-time availability.  |
| **Booking Tracker**  | Look up workshop appointment statuses instantly via mobile number.                                   |
| **Compatibility Check**| Select Brand → Model → Year to quickly find matching auto parts and accessories via a dynamic UI.                 |
| **AI Mechanic Bot**  | Floating assistant powered by Claude (Anthropic API) that answers mechanic and product queries.     |
| **User Profiles**    | User registration, login, view past orders, edit profile details securely.                          |
| **Gallery**          | Masonry photo gallery filterable by category showcasing completed car transformations.              |
| **Information**      | Contact form with spam protection, About Us, FAQ, Privacy Policy, Terms of Service.               |

**Cross-cutting Capabilities:**
- 🌍 **Multi-language:** Seamlessly switch between English, Bahasa Melayu, and Chinese (session-based).
- 🌓 **Theming:** Fast Dark / Light / System theme toggling.
- 📱 **Responsive Design:** Completely optimized for mobile, tablet, and desktop browsing.
- 🚀 **SEO & Accessibility:** Per-page SEO, Auto-generated XML sitemaps, ARIA labels, and Open Graph previews.

### 🛡️ Admin Dashboard (`/admin`)

Powered by **Filament 5**, offering an intuitive management system:

| Resource       | Capabilities |
| -------------- | ------------ |
| **Dashboard**  | KPI metrics — revenue, daily pending bookings, unread enquiries, order/sales overviews. |
| **Orders**     | View and manage customer e-commerce orders, update fulfillment and shipping statuses. |
| **Products**   | Manage catalogs, pricing, stock, vehicle compatibility, and rich media library image sets. |
| **Categories** | Manage hierarchical product categories. |
| **Services**   | Manage bookable workshop services (price, duration, availability toggle). |
| **Bookings**   | Administer appointments, track pending and scheduled workshop slots. |
| **Contacts / FAQ** | View customer enquiries, reply setups, and manage Frequently Asked Questions. |
| **Feedback**   | Moderate and approve customer testimonials & reviews. |
| **Gallery**    | Organize showcase albums using automated masonry grid image conversions. |
| **Users / Logs** | Manage staff/system accounts and track activity logs natively (Spatie ActivityLogs). |
| **Settings**   | Top-level global system settings configurations. |

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel 13 (PHP 8.3+)
- **Reactive UI:** Livewire 4
- **Admin Panel:** Filament 5
- **Styling:** Tailwind CSS (CDN — zero build step required)
- **Database:** SQLite (dev) / MySQL (production)
- **Image Management:** Spatie MediaLibrary 11 + Intervention Image 4 (Auto-resizing thumbnails/cards)
- **AI Integrations:** Anthropic Claude API (`claude-haiku-4-5`)
- **Other Packages:**
  - `spatie/laravel-activitylog` (Audit trails)
  - `spatie/laravel-sitemap` (Dynamic sitemaps)
  - `artesaos/seotools` (SEO configurations)
  - `barryvdh/laravel-debugbar` (Development tools)

---

## 🚀 Quick Start

### 1. System Requirements
- **PHP >= 8.3** (Required Extensions: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`)
- **Composer >= 2**
- *No Node/NPM required (Tailwind delivered via CDN)*

### 2. Installation

Clone the repository and install the dependencies. This might take a minute upon initial run:
```bash
git clone <repo-url> winwin
cd winwin
composer install
```

### 3. Environment Setup

Duplicate the standard environment file and generate a unique application key:
```bash
cp .env.example .env
php artisan key:generate
```

_Optional:_ If you want to enable the AI mechanic chatbot, insert your API key into `.env`:
```env
ANTHROPIC_API_KEY=sk-ant-...
```
*(Without a key, the chatbot hides intelligently and gets replaced with a standard contact prompt).*

Update necessary operational credentials inside `.env` (like `STORE_NAME`, `STORE_PHONE_DISPLAY`, etc.).

### 4. Database Initialization

Execute migrations to establish the SQLite datastore and populate initial dummy seed data:
```bash
php artisan migrate --seed
```
*This routine seamlessly seeds dummy products, 31 Malaysian car models, testimonials, and a default super-admin profile (`admin@example.com` / `password`).*

### 5. Assets Linking

Link local storage directories for public media access and generate an initial SEO sitemap:
```bash
php artisan storage:link
php artisan sitemap:generate
```

### 6. Boot Application

Initiate the Laravel local development server along with the background queue:
```bash
php artisan serve
```
*(Optionally run `composer run dev` to boot queue workers and servers parallelly).*

- Customer storefront natively bound to: `http://localhost:8000`
- Administrator portal securely localized at: `http://localhost:8000/admin`

---

## 📸 Media & Queue Workers

Products, Services, and Gallery items are reliably handled traversing **Spatie Laravel MediaLibrary**. Images are auto-processed (cropped/compressed) asynchronously safely in background. 

For local development image uploads to generate correctly, assure the queue worker process is active:
```bash
php artisan queue:listen
```
Alternatively, switch the connection variable exclusively: `QUEUE_CONNECTION=sync` under `.env`.

---

## 🌐 Deploying to Production

When publishing to a live monolithic server, verify the `.env` variables are tightly closed for production:
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql # Or your persistent relational choice
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

Execute production optimization commands natively shipped with Laravel:
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan sitemap:generate
php artisan optimize
```

Implement standard Cron scheduling inside your host for continuous jobs (e.g. daily Sitemaps):
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```
And set up a dedicated Supervisor daemon for your exact `php artisan queue:work --daemon`.

---

## 💻 Windows / Herd Operations

For native *Laravel Herd on Windows* configurations, patch `.env` if encountering "expired session" states during authentication attempts:
```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

If resolving permission clashes for SQLite writes (`rename(): Access is denied`):
```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
php artisan view:clear
```

---

## 📄 License & Credits
Built exclusively for internal capstone architecture design & demonstration scopes. All utilized assets are for demonstration purposes strictly.
