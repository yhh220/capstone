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

## About

**Win Win Car Studio** is a full-stack web application built as a capstone project for academic demonstration purposes.

It simulates a real-world Malaysian car accessories showroom — allowing customers to browse products, book workshop appointments, and contact the store, while the owner manages everything through an admin panel.

The project is built on the **TALL stack**: Tailwind CSS, Alpine.js, Livewire, and Laravel.

> **Note:** This project is for educational use only. All sample data, images, and business information are fictional and created solely for demonstration.

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13 (PHP 8.3+) |
| Reactive UI | Livewire 4.2 |
| Admin Panel | Filament 5 |
| Styling | Tailwind CSS (CDN) |
| Database | SQLite (dev) / MySQL (production) |
| Media | Spatie Laravel MediaLibrary |
| Language | EN / BM / ZH (trilingual) |

---

## Getting Started

```bash
# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed demo data
php artisan migrate --seed

# Link storage
php artisan storage:link

# Start the server
php artisan serve
```

- Storefront: `http://localhost:8000`  
- Admin panel: `http://localhost:8000/admin`  
- Default admin: `admin@example.com` / `password`

### Windows Note

If you get a `rename(): Access is denied` error:

```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
php artisan view:clear
```

---

## License

Built for academic capstone submission. Not intended for commercial use.
