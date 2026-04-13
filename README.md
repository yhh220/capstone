# Win Win Car Studio

<p>
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 4">
  <img src="https://img.shields.io/badge/Filament-5-F59E0B?style=for-the-badge" alt="Filament 5">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/SQLite-dev-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
</p>

A full-stack web platform for a Malaysian car accessories showroom, built with **Laravel 13**, **Livewire 4**, and **Filament 5**. Customers browse products, book installation services, check vehicle compatibility, and contact the store — in English, Malay, or Chinese. The store owner manages everything through a dedicated admin panel.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Admin Panel Setup](#admin-panel-setup)
- [Running the App](#running-the-app)
- [Image Uploads](#image-uploads)
- [SEO](#seo)
- [Sitemap](#sitemap)
- [Debugbar](#debugbar)
- [Deploying to Production](#deploying-to-production)
- [Windows / Herd Notes](#windows--herd-notes)
- [Project Structure](#project-structure)

---

## Features

### Customer Storefront

| Page | Description |
|---|---|
| **Home** | Hero, featured products, category grid, stats, testimonials, CTA |
| **Products** | Full catalog with live search, category filter, price range, pagination |
| **Product Detail** | Image, description, WhatsApp enquiry, related products |
| **Services** | Bookable services with pricing and duration |
| **Booking** | Book by date and time slot with real-time availability |
| **Booking Tracker** | Look up appointment status by phone number |
| **Gallery** | Masonry photo gallery filterable by category |
| **Compatibility Checker** | Select brand → model → year to find matching products |
| **AI Chatbot** | Floating assistant powered by Claude (Anthropic API) |
| **Contact** | Enquiry form with rate limiting and honeypot spam protection |
| **About** | Store story, team, mission, values |

**Cross-cutting:**
- Multi-language: English / Bahasa Melayu / 中文 (session-based)
- Dark / Light / System theme toggle
- User registration and login with password strength indicator
- Fully responsive — mobile, tablet, desktop
- Per-page SEO: meta title, description, Open Graph, JSON-LD
- Auto-generated sitemap at `/sitemap.xml`
- Accessibility: skip links, ARIA labels, reduced-motion support

### Admin Panel (`/admin`)

| Resource | Capabilities |
|---|---|
| **Products** | Create / edit with media library upload, featured flag, pricing |
| **Categories** | Manage product categories |
| **Services** | Manage bookable services with price, duration, toggle |
| **Bookings** | View and manage appointments; pending badge count |
| **Gallery** | Upload and organise gallery images by category |
| **Feedback** | Approve / reject customer reviews |
| **Contacts** | View enquiries, mark as read |
| **Audit Log** | Read-only activity history (Spatie ActivityLog) |
| **Dashboard** | Stats — revenue, pending bookings, enquiries, new contacts |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 (PHP 8.3+) |
| Reactive UI | Livewire 4 |
| Admin Panel | Filament 5 |
| Styling | Tailwind CSS (CDN — no build step required) |
| Database | SQLite (dev) / MySQL (production) |
| Image Management | Spatie Laravel MediaLibrary 11 + Intervention Image |
| SEO | artesaos/seotools |
| Sitemap | spatie/laravel-sitemap |
| Activity Log | spatie/laravel-activitylog 5 |
| AI Chatbot | Anthropic Claude API (`claude-haiku-4-5`) |
| Debug toolbar | barryvdh/laravel-debugbar (dev only) |

---

## Requirements

Before you start, make sure you have:

- **PHP >= 8.3** with the following extensions enabled:
  - `pdo_sqlite` (or `pdo_mysql` for MySQL)
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `gd` (required for image resizing)
- **Composer >= 2**
- No Node.js or npm required — Tailwind CSS loads via CDN

To check your PHP version and extensions:

```bash
php -v
php -m | grep -E "gd|sqlite|mbstring"
```

---

## Installation

Follow these steps in order after cloning the repository.

### Step 1 — Clone the repository

```bash
git clone <repo-url> winwin
cd winwin
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

This installs Laravel, Livewire, Filament, and all other packages listed in `composer.json`. It may take 1–2 minutes on first run.

### Step 3 — Create the environment file

```bash
cp .env.example .env
```

Then generate the application key:

```bash
php artisan key:generate
```

You should see:

```
INFO  Application key set successfully.
```

### Step 4 — Configure your environment

Open `.env` and fill in your store details. At minimum, update these:

```env
APP_NAME="Win Win Car Studio"
APP_URL=http://localhost:8000

# Store information (shown in nav, footer, WhatsApp links)
STORE_NAME="WIN WIN CAR AUDIO AUTO ACCESSORIES"
STORE_SHORT_NAME="WIN WIN"
STORE_TAGLINE="CAR AUDIO"
STORE_PHONE_DISPLAY="016-9150917"
STORE_PHONE_RAW="60169150917"
STORE_EMAIL="winwincaraudio@gmail.com"
STORE_ADDRESS="NO. 22, GROUND FLOOR, JALAN DINAR C U3/C, TAMAN SUBANG PERDANA, 40150 SHAH ALAM"
STORE_HOURS="Mon–Thu, Sat–Sun: 10am–7pm (Closed Friday)"
STORE_FACEBOOK_URL="https://facebook.com/yourpage"
```

### Step 5 — Set up the database

The app uses SQLite by default — no database server required.

```bash
php artisan migrate --seed
```

This will:
- Create all database tables
- Seed 8 product categories and 12 sample products
- Seed 31 Malaysian car models (Proton, Perodua, Honda, Toyota, Mazda, etc.)
- Seed 3 sample customer testimonials
- **Create a default admin account** — `admin@example.com` / `password`

You should see output ending with:

```
INFO  Seeding: Database\Seeders\DatabaseSeeder
INFO  Seeding: Database\Seeders\CarModelSeeder
INFO  Database seeding completed successfully.
```

### Step 6 — Link storage for uploads

```bash
php artisan storage:link
```

This creates a `public/storage` symlink so uploaded images are publicly accessible. You should see:

```
INFO  The [public/storage] link has been connected to [storage/app/public].
```

### Step 7 — Generate the sitemap

```bash
php artisan sitemap:generate
```

This creates `public/sitemap.xml`. You can re-run this any time after adding new products.

### Step 8 — Start the development server

```bash
php artisan serve
```

The app is now running at:

| URL | What you see |
|---|---|
| `http://localhost:8000` | Customer storefront |
| `http://localhost:8000/admin` | Admin panel login |

---

## Environment Setup

### AI Chatbot (optional)

To enable the AI chatbot, add your Anthropic API key to `.env`:

```env
ANTHROPIC_API_KEY=sk-ant-...
```

Without a key, the chatbot is automatically hidden and replaced with a contact prompt. You can get a key at [console.anthropic.com](https://console.anthropic.com).

### Google Search Console (optional)

To verify your site with Google:

```env
GOOGLE_SITE_VERIFICATION=your-verification-code
```

---

## Admin Panel Setup

The admin panel lives at `/admin`. It is powered by **Filament 5** and is separate from the customer-facing site.

### Logging in

The seeder creates a default admin account automatically:

- **URL:** `http://localhost:8000/admin`
- **Email:** `admin@example.com`
- **Password:** `password`

> Change your password after first login.

### Changing your admin password

After logging in, click your name in the bottom-left corner of the admin panel → **Profile** → update your password there.

Alternatively, from the terminal:

```bash
php artisan tinker
```

Then inside Tinker:

```php
$user = \App\Models\User::where('email', 'admin@example.com')->first();
$user->password = bcrypt('your-new-password');
$user->save();
exit
```

### Creating additional admin accounts

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name'     => 'Staff Name',
    'email'    => 'staff@example.com',
    'password' => bcrypt('their-password'),
    'role'     => 'owner',   // 'owner' = full admin access
]);
exit
```

### Admin roles

| Role | Access |
|---|---|
| `owner` | Full admin panel access |
| (anything else) | Customer account only, no `/admin` access |

---

## Running the App

### Development (standard)

```bash
php artisan serve
```

### Development (all services at once)

If you want the server, queue worker, and log watcher running together:

```bash
composer run dev
```

This starts:
- `php artisan serve` — web server
- `php artisan queue:listen` — background job queue (for media conversions)
- `php artisan pail` — real-time log viewer
- `npm run dev` — Vite (if you add custom JS/CSS later)

> **Note:** Media conversions (thumbnails) are dispatched to the queue. If you upload an image and the thumbnail does not appear, make sure the queue worker is running (`php artisan queue:listen`) or switch to synchronous processing by setting `QUEUE_CONNECTION=sync` in `.env`.

### Useful commands

```bash
# Regenerate sitemap after adding products
php artisan sitemap:generate

# Regenerate all image thumbnails
php artisan media-library:regenerate

# Clear all caches
php artisan optimize:clear

# View all registered routes
php artisan route:list

# Open interactive console
php artisan tinker
```

---

## Image Uploads

Products, Services, and Gallery items use **Spatie Laravel MediaLibrary** for image management. Images uploaded through the Filament admin panel are automatically resized into multiple versions:

| Model | Conversions generated |
|---|---|
| **Product** | `thumb` (400×300 px), `card` (800×600 px) |
| **Service** | `thumb` (600×400 px) |
| **Gallery Item** | `thumb` (600×600 px), `full` (1200 px wide) |

All conversions are optimised and stored alongside the original. The admin upload form includes a built-in image editor (crop, rotate, flip).

**In Blade views**, use the model helper to get the right size:

```php
$product->getImageUrl('thumb')   {{-- small thumbnail --}}
$product->getImageUrl('card')    {{-- medium card image --}}
$product->getImageUrl()          {{-- original upload --}}
```

Existing images uploaded before MediaLibrary was added continue to work automatically via fallback.

---

## SEO

Every page sets its own title, meta description, Open Graph tags, and JSON-LD through the `SetsSeo` trait.

**To add SEO to a new Livewire page:**

```php
use App\Livewire\Concerns\SetsSeo;

class MyPage extends Component
{
    use SetsSeo;

    public function mount(): void
    {
        $this->setSeo(
            title: 'Page Title',
            description: 'Short description shown in Google results (under 160 chars).',
            imageUrl: 'https://yoursite.com/og-image.jpg', // optional, for social sharing
        );
    }
}
```

The page title will automatically be formatted as: `Page Title | Win Win Car Studio`.

---

## Sitemap

The sitemap at `/sitemap.xml` is generated from all public pages and active products.

```bash
# Generate or refresh manually
php artisan sitemap:generate
```

To have it regenerate automatically every day, add this cron entry to your server:

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Submit the sitemap URL to [Google Search Console](https://search.google.com/search-console) after deploying.

---

## Debugbar

`barryvdh/laravel-debugbar` is installed as a dev dependency. It shows a debug toolbar in the browser with query counts, request info, memory usage, and more.

- **Visible when:** `APP_DEBUG=true` in `.env`
- **Hidden when:** `APP_DEBUG=false` in `.env`

No configuration needed. Set `APP_DEBUG=false` before deploying to production and the toolbar disappears completely.

---

## Deploying to Production

When deploying to a live server, update `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Switch to MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=winwin_db
DB_USERNAME=db_user
DB_PASSWORD=db_password

# Use real queue driver (database or redis)
QUEUE_CONNECTION=database

# Use file or redis for session (not cookie in production)
SESSION_DRIVER=file
```

Then run:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan sitemap:generate
php artisan optimize
```

Set up the cron for scheduled tasks (sitemap regeneration):

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Set up a queue worker to process image conversions:

```bash
php artisan queue:work --daemon
```

Or use Supervisor to keep it running persistently on the server.

---

## Windows / Herd Notes

### Session issues — "This page has expired" on login

On Laravel Herd for Windows, use cookie-based sessions:

```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

### Storage permission errors — `rename(): Access is denied`

```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
php artisan view:clear
```

---

## Project Structure

```
app/
├── Console/Commands/
│   └── GenerateSitemap.php         # php artisan sitemap:generate
│
├── Filament/
│   ├── Resources/                  # Admin panel CRUD
│   │   ├── Products/               # Product management
│   │   ├── Services/               # Service management
│   │   ├── Bookings/               # Appointment management
│   │   ├── Gallery/                # Gallery management
│   │   ├── Feedback/               # Review management
│   │   ├── Contacts/               # Enquiry management
│   │   ├── Users/                  # User management
│   │   ├── Categories/             # Category management
│   │   └── Activities/             # Audit log (read-only)
│   └── Widgets/                    # Dashboard stats + activity feed
│
├── Http/Middleware/
│   ├── AdminMiddleware.php         # Protects /admin routes
│   ├── SetLocale.php               # Applies session language
│   └── SecurityHeaders.php         # CSP and security response headers
│
├── Livewire/
│   ├── Concerns/
│   │   └── SetsSeo.php             # Shared SEO trait for all pages
│   ├── Auth/
│   │   └── UserLogin.php           # Login + register (tab UI)
│   ├── HomePage.php
│   ├── ProductsPage.php
│   ├── ProductDetail.php
│   ├── ServicesPage.php
│   ├── BookingForm.php
│   ├── BookingTracker.php
│   ├── GalleryPage.php
│   ├── ContactPage.php
│   ├── AboutPage.php
│   ├── AiChatbot.php
│   └── CompatibilityChecker.php
│
└── Models/
    ├── Product.php                 # HasMedia (thumb, card), ActivityLog
    ├── Service.php                 # HasMedia (thumb), ActivityLog
    ├── GalleryItem.php             # HasMedia (thumb, full), ActivityLog
    ├── Booking.php
    ├── Category.php
    ├── Contact.php
    ├── Feedback.php
    ├── CarModel.php
    └── User.php

config/
├── seotools.php                    # SEO defaults (title, description, OG, JSON-LD)
├── media-library.php               # Spatie MediaLibrary settings
├── sitemap.php                     # Sitemap crawl config
└── services.php                    # Store info (name, phone, address, etc.)

database/
├── migrations/                     # All schema migrations
└── seeders/
    ├── DatabaseSeeder.php          # Admin user, categories, products, testimonials
    └── CarModelSeeder.php          # 31 Malaysian car models

lang/
├── ms.json                         # Bahasa Melayu translations
└── zh.json                         # Chinese translations

resources/views/
├── layouts/
│   └── app.blade.php               # Main layout — nav, dark mode, SEO tags, footer
├── components/
│   ├── chatbot.blade.php
│   ├── compatibility-checker.blade.php
│   ├── empty-state.blade.php
│   └── page-loader.blade.php
└── livewire/                       # One Blade view per Livewire component

routes/
├── web.php                         # All public routes + /sitemap.xml
└── console.php                     # Scheduled commands (sitemap:generate daily)
```

---

## License

This project is built for educational / capstone purposes.
