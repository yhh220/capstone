# Win Win Car Studio — Auto Accessories Platform

**Win Win Car Studio** is a full-stack e-commerce and booking platform built for a Malaysian car accessories showroom. Customers can browse products, book installation services, check vehicle compatibility, view the gallery, and chat with an AI assistant — all in English, Malay, or Chinese.

---

## Features

### Customer-Facing Storefront

| Feature | Description |
|---|---|
| Homepage | Hero section, featured products, category browsing, stats |
| Products | Full catalog with category filter, price range filter, search |
| Product Detail | Image gallery, compatibility info, WhatsApp inquiry button |
| Services | List of available installation/modification services |
| Booking | Book a service by date/time slot, with booking tracker by phone |
| Gallery | Filterable photo gallery (audio, tint, accessories, modification) |
| Compatibility Checker | Select brand → model → year to find compatible products |
| AI Chatbot | Floating chat powered by Claude (Anthropic API) |
| Feedback / Reviews | Customer testimonials approved via admin |
| Contact | Enquiry form with spam honeypot protection |
| Multi-language | English / Bahasa Melayu / 中文 (session-based switcher) |
| Dark Mode | System-aware dark/light toggle |
| User Auth | Register, login, remember me, password strength indicator |

### Admin Panel (`/admin`)

| Resource | Capabilities |
|---|---|
| Products | Create/edit with image upload, featured flag, stock, pricing |
| Categories | Manage product categories |
| Services | Manage bookable services with price, duration, active toggle |
| Bookings | View/manage appointments, badge count for pending bookings |
| Gallery | Upload and organize gallery images by category |
| Feedback | Approve/reject customer reviews |
| Contacts | View and mark enquiries as read |
| Audit Log | Read-only activity log powered by Spatie ActivityLog |
| Dashboard | Stats widget (revenue, pending bookings, enquiries, appointments) |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13.4 (PHP 8.3+) |
| Reactive UI | Livewire 4.2 |
| Admin Panel | Filament 5.5 |
| Styling | Tailwind CSS (CDN — no build step required) |
| Database | SQLite (dev) / MySQL (production) |
| Activity Log | Spatie Laravel ActivityLog v5 |
| AI Chatbot | Anthropic Claude API (`claude-haiku-4-5`) |
| Local Dev | Laravel Herd (Windows/macOS) |

---

## Getting Started

### Prerequisites

- PHP >= 8.3 with extensions: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`
- Composer
- No Node.js or npm required — Tailwind is loaded via CDN

### Installation

1. **Clone and enter the project:**
   ```bash
   git clone <repo-url> capstone
   cd capstone
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations and seed data:**
   ```bash
   php artisan migrate --seed
   ```
   This creates all tables and seeds 31 Malaysian car models (Proton, Perodua, Honda, Toyota, Mazda).

5. **Create an admin user:**
   ```bash
   php artisan make:filament-user
   ```

6. **Start the server:**
   ```bash
   php artisan serve
   ```
   App available at `http://localhost:8000` — admin panel at `http://localhost:8000/admin`.

### Optional: AI Chatbot

Add your Anthropic API key to `.env` to enable the AI chatbot:
```env
ANTHROPIC_API_KEY=sk-ant-...
```
Without a key the chatbot gracefully falls back to a contact prompt.

---

## Environment Notes

### Session Driver (Windows / Herd)

If running on **Laravel Herd on Windows**, use `SESSION_DRIVER=cookie` to avoid PHP-FPM file-locking issues that cause "This page has expired" on login:

```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

### Storage Permissions (Windows)

If you see `rename(): Access is denied` errors, grant write permissions to the storage folder:
```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
php artisan view:clear
```

---

## Project Structure (Key Directories)

```
app/
├── Filament/Resources/     # Admin panel resources (Products, Bookings, etc.)
├── Filament/Widgets/       # Dashboard stats + activity log widget
├── Http/Middleware/        # AdminMiddleware, SetLocale, SecurityHeaders
├── Livewire/               # All frontend reactive components
│   ├── AiChatbot.php
│   ├── BookingForm.php
│   ├── CompatibilityChecker.php
│   ├── GalleryPage.php
│   ├── ServicesPage.php
│   └── ...
└── Models/                 # Eloquent models with activity logging

database/
├── migrations/             # All schema migrations
└── seeders/
    └── CarModelSeeder.php  # 31 Malaysian car models

lang/
├── ms.json                 # Bahasa Melayu translations
└── zh.json                 # Chinese translations

resources/views/
├── layouts/app.blade.php   # Main layout (nav, dark mode, language switcher)
└── livewire/               # All Blade views for Livewire components
```

---

## License

This project is for educational/capstone purposes.
