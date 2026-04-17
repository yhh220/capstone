# Win Win Car Audio — Capstone Project

## Project Overview
Online auto accessories showroom for Win Win Car Audio Auto Accessories, Shah Alam.
A capstone project (INTI IICS, DCS APR 2026).

## Tech Stack
- **Framework:** Laravel 13 + Livewire 3 + Filament 3
- **Styling:** Tailwind CSS
- **DB (dev):** SQLite / MySQL (prod)
- **Build:** Vite
- **AI (dev/demo):** Ollama + Gemma-SEA-LION-v4-4B-VL (local, free)
- **AI (prod):** MockDriver (AI off by default)

## Team Roles
- **YHH (Chin Yi Hang):** All web pages + UI + AI module + Service booking
- **Chris (Shee Zhen Hong):** Online shopping + Cart/Checkout/Orders + Admin Filament + DB migrations + Deployment
- **Soon Gor (Wong Soon Heng):** 3D only — Homepage 3D showcase + Product detail 3D viewer

## Key Business Rules
- **Online shopping toggle:** `settings` table, key = `ONLINE_SHOPPING_ENABLED`, value = `'true'`/`'false'`
  - Toggle OFF: hide prices, hide Add to Cart, redirect /cart and /checkout to home
  - Toggle ON: full e-commerce enabled
  - Use helper: `setting('ONLINE_SHOPPING_ENABLED')`
- **AI driver:** controlled by `.env` `AI_DRIVER` = `mock` (prod) or `ollama` (dev/demo)
- **Payment:** mock only — no real payment, clicking "Place Order" directly confirms
- **Booking:** slot conflict detection required, buffer_after minutes must be respected

## Database Tables
**Existing:** categories, products (need new fields), feedbacks, contacts
**To create:** services, bookings, orders, order_items, cart_items, settings, ai_logs

### Products — new fields needed
```
stock (int), brand (string), description_ms (text), description_zh (text),
specs (json), compatible_vehicles (json), model_url (string nullable), has_3d (bool)
```

### Settings seed
```
ONLINE_SHOPPING_ENABLED = false
BUSINESS_HOURS_START = 09:00
BUSINESS_HOURS_END = 18:00
```

## AI Module Architecture
```
app/Contracts/AiServiceInterface.php   — interface
app/Services/Ai/MockDriver.php         — returns placeholder, used in prod
app/Services/Ai/OllamaDriver.php       — calls localhost:11434, used in dev
app/Providers/AiServiceProvider.php    — binds driver from config('ai.driver')
config/ai.php                          — driver, ollama host/model config
```

**Ollama endpoint:** `POST http://127.0.0.1:11434/api/chat`
**Model:** `aisingapore/Gemma-SEA-LION-v4-4B-VL`
**Two features:**
1. Product recommendation (customer-facing, Livewire component)
2. Description generator (admin Filament action on ProductResource)

## 3D Integration Points (Soon Gor's mount points)
```blade
{{-- Homepage --}}
<div id="3d-mount-homepage" data-product-slug="skynavi-android-player"></div>

{{-- Product detail (only if product has model) --}}
@if($product->has_3d)
<div id="3d-mount-product"
     data-model-url="{{ $product->model_url }}"
     data-product-name="{{ $product->name }}"></div>
@endif
```
Soon Gor owns everything inside these divs. YHH provides the mount points only.

## Key Routes
```
/                    Home
/products            Catalog (filter/sort/search/paginate)
/products/{slug}     Detail (gallery + 3D mount + specs + booking link)
/services            Services list
/booking             Booking wizard (Livewire, 4 steps)
/booking/{token}     Manage booking (cancel/reschedule)
/cart                Cart (session-based, middleware: ShoppingEnabled)
/checkout            Checkout wizard (3 steps, middleware: ShoppingEnabled)
/track-order         Order tracking (order# + email lookup)
/contact             Contact form + map
/faq                 FAQ accordion
/lang/{locale}       Switch language (en/ms/zh)
/admin/*             Filament admin panel
```

## Email (Laravel Mail)
- Use Mailtrap sandbox for dev (free)
- Mailables needed: OrderConfirmationMail, OrderStatusUpdateMail, BookingConfirmationMail, BookingStatusUpdateMail
- From: noreply@winwincaraudio.my, "Win Win Car Audio"

## Coding Conventions
- Livewire components for all interactive UI
- Filament resources for all admin CRUD
- `setting()` helper for all settings lookups (cached 3600s, clear cache on save)
- Blade `@if(setting('ONLINE_SHOPPING_ENABLED') === 'true')` for toggle checks
- Harvard referencing in report (not our concern here)
- Commit format: `feat(booking): add slot conflict detection`

## Environment Variables (see .env.example)
```
AI_DRIVER=mock
OLLAMA_HOST=http://127.0.0.1:11434
OLLAMA_MODEL=aisingapore/Gemma-SEA-LION-v4-4B-VL
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@winwincaraudio.my
MAIL_FROM_NAME="Win Win Car Audio"
```

## Company Info (for seeding / content)
- **Name:** Win Win Car Audio Auto Accessories (002645107-U)
- **Address:** No. 22, Ground Floor, Jalan Dinar CU3/C, Taman Subang Perdana, Seksyen U3, 40150 Shah Alam, Selangor
- **Phone:** 016-915 0917
- **Facebook:** facebook.com/winwincaraudio
- **Brands:** Mohawk, 70mai, Alpine, Skynavi, Sparko
- **Products:** Android player, Dash cam, Speaker 6×9, Tweeter, Number plate, Tinted, Bodykit, Wiper, LED light, Lamp cover, Car wash shampoo
- **Services:** Aircond installation, Aircond gas refill, Oil compressor — NO rim change
