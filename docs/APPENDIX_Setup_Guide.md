# Appendix: Installation & Setup Guide (Running the System Locally)

This guide explains how to run the Win Win Car Audio system on a local machine
for evaluation. Locally the system uses **SQLite** (no database server to
install), **demo** payment mode (no Stripe keys needed), and the **log** mail
driver (no email account needed), so it runs with minimal configuration.

## Prerequisites
- **PHP 8.4 or newer**, with the common extensions enabled: `pdo_sqlite`,
  `mbstring`, `openssl`, `fileinfo`, `gd` (for image processing), `zip`.
- **Composer** (PHP dependency manager)
- **Node.js** with **npm** (for building the front-end assets)

A local PHP environment such as **Laravel Herd** provides PHP, Composer and a
`.test` domain in one install and is the simplest option on Windows/macOS.

## Setup Steps

1. **Get the code** — clone the repository (or unzip the submitted archive) and
   open a terminal in the project folder.

2. **Install PHP dependencies:**
   ```
   composer install
   ```

3. **Create the environment file:**
   ```
   copy .env.example .env      (Windows)
   cp .env.example .env        (macOS/Linux)
   ```

4. **Generate the application key:**
   ```
   php artisan key:generate
   ```

5. **⚠️ Set the admin login in `.env`** (required — there is deliberately no
   default admin account, to avoid a publicly-known owner login). Add these two
   lines to `.env` with values of your choice:
   ```
   DEFAULT_ADMIN_EMAIL=owner@winwin.test
   DEFAULT_ADMIN_PASSWORD=YourStrongPassword123
   ```
   You will use these to log in to the admin panel later.

6. **Make sure the SQLite database file exists.** The repository ships an empty
   `database/database.sqlite`; if it is missing, create an empty file at that
   path.

7. **Create the database tables:**
   ```
   php artisan migrate
   ```

8. **⚠️ Seed the data (do not skip this):**
   ```
   php artisan db:seed
   ```
   This loads the product catalogue, categories, brands, services, FAQ, and the
   admin account, **and copies the product images into storage**. Without this
   step the site opens **empty** (no products, no images) and you cannot log in.

9. **⚠️ Link the storage folder** so uploaded and product images are served:
   ```
   php artisan storage:link
   ```

10. **Build the front-end assets:**
    ```
    npm install
    npm run build
    ```

11. **Start the site:**
    ```
    php artisan serve
    ```
    Then open **http://localhost:8000** in a browser. (With Laravel Herd, the
    site is instead served at its `.test` domain automatically — no `serve`
    command needed.)

12. **Log in to the admin panel** at **http://localhost:8000/admin** using the
    `DEFAULT_ADMIN_EMAIL` / `DEFAULT_ADMIN_PASSWORD` you set in Step 5.

## Quick Version (for someone comfortable with the terminal)
```
composer install
cp .env.example .env
php artisan key:generate
# add DEFAULT_ADMIN_EMAIL and DEFAULT_ADMIN_PASSWORD to .env
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

## Notes for the Evaluator
- **Database:** local runs on SQLite (zero-config); production uses MySQL/TiDB,
  switched only by environment variables — no code changes.
- **Payments:** default is **demo mode**, which simulates payment with no real
  gateway; no Stripe keys are required to evaluate the ordering flow.
- **Email:** default mail driver is **log** — "sent" emails are written to
  `storage/logs/laravel.log` instead of being delivered, so no mail account is
  needed. You can read them there to confirm an email would have been sent.
- **3D models:** the compressed 3D models are included in the repository
  (`public/models/3d/*-draco.glb`), so the configurator works offline.
- **Running the tests** (optional): `php artisan test` runs the full automated
  suite against an in-memory database.

## Common Issues
| Symptom | Cause / Fix |
|---|---|
| Site opens but has **no products or images** | `php artisan db:seed` was not run (Step 8) |
| Product images show as **broken** | `php artisan storage:link` was not run (Step 9) |
| Seeding **errors out** immediately | `DEFAULT_ADMIN_EMAIL` / `DEFAULT_ADMIN_PASSWORD` not set in `.env` (Step 5) |
| Page loads with **unstyled / no layout** | `npm run build` was not run (Step 10) |
| "database file does not exist" | the `database/database.sqlite` file is missing (Step 6) |
