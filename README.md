# Win Win Car Studio

Malaysian car-accessories e-commerce and workshop-booking platform, built as an academic capstone project.

Customers can browse products and services, make workshop bookings, track bookings and orders, and use a trilingual assistant. Staff manage the catalogue, bookings, orders, users, site settings, and activity logs through the Filament admin panel.

> All business details, customer data, products, and media in this repository are fictional and provided for educational demonstration only.

## Features

- Customer storefront: products, services, booking, order tracking, FAQ, contact pages, and a 3D configurator.
- E-commerce flow: cart, checkout, simulated payment, order history, and invoice download.
- Workshop bookings: available time slots, booking references, and guest booking lookup.
- Admin panel: catalogue, orders, bookings, customers, staff, settings, and audit logs.
- English, Bahasa Melayu, and Chinese localisation.
- Light/dark mode, accessibility-focused UI, rate limiting, honeypot protection, and security headers.

## Tech Stack

| Area | Technology |
| --- | --- |
| Backend | Laravel 13 / PHP 8.3+ |
| Interactive UI | Livewire 4 / Alpine.js |
| Admin panel | Filament 5 |
| Frontend build | Tailwind CSS 4 / Vite |
| 3D configurator | Three.js |
| Database | SQLite (local development) / MySQL-compatible database (deployment) |
| Media handling | Spatie Media Library |

## Requirements

- PHP 8.3 or later, with SQLite enabled
- Composer 2
- Node.js 20 or later and npm
- Git

## Local Setup

From the project directory, run:

```bash
composer install
npm install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
```

Before seeding, add a private administrator account to `.env`:

```dotenv
DEFAULT_ADMIN_EMAIL=admin@example.test
DEFAULT_ADMIN_PASSWORD=choose-a-strong-local-password
```

Then initialise the application and build the frontend assets:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000` for the storefront. Sign in to the admin panel at `http://127.0.0.1:8000/admin` with the email and password set above.

For frontend development with hot reloading, run this in a second terminal:

```bash
npm run dev
```

### Windows PowerShell

Use these equivalents for the two Unix commands above:

```powershell
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
```

## Development Defaults

- The seeded payment mode is `demo`; no real payment gateway is required for local use.
- Email uses Laravel's `log` mailer by default, so messages are written to the application logs instead of being sent.
- Stripe test payments, Google/Microsoft sign-in, and Gmail API sending are optional. Their environment-variable placeholders and comments are in `.env.example`.
- Never commit `.env`, real passwords, Stripe keys, OAuth secrets, or mail credentials.

## Tests and Quality Checks

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

To reset the local database and recreate all sample data:

```bash
php artisan migrate:fresh --seed
```

## Project Scope

This group capstone project demonstrates an end-to-end web application: a customer-facing storefront, e-commerce and workshop workflows, a role-based administration area, localisation, and operational safeguards suitable for a small-business prototype.

## Live Demo

- Storefront: [winwincaraudio.onrender.com](https://winwincaraudio.onrender.com)
- Admin panel: [winwincaraudio.onrender.com/admin](https://winwincaraudio.onrender.com/admin)

## License

Built for academic capstone submission. Not intended for commercial use.

Copyright © 2026 INTI International College Subang. All rights reserved.
