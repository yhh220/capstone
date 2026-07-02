<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="380" alt="Laravel Logo">

# Win Win Car Studio

**Malaysian Car Accessories E-Commerce & Workshop Booking Platform**

<br>

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4.2-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5-F59E0B?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-Dev-003B57?style=for-the-badge&logo=sqlite&logoColor=white)

</div>

---

## About

**Win Win Car Studio** is a full-stack capstone project that simulates a Malaysian car-accessories showroom. Customers can browse products, book workshop appointments, track orders/bookings, and chat with a trilingual assistant; staff manage everything through a Filament admin panel.

Built on the **TALL stack** (Tailwind, Alpine.js, Livewire, Laravel).

> For educational use only. All sample data, images, and business details are fictional.

---

## Features

- **Storefront** — Home, Products, Services, Booking, About, Contact, FAQ, 3D configurator
- **Online Shopping** — cart, checkout, order history; toggleable view-only vs shopping mode (admin setting)
- **Bookings & Orders** — appointment booking with time slots; guest lookup by reference + phone/email
- **Trilingual (EN / BM / ZH)** — full i18n with translation-coverage tests
- **Chatbot** — keyword assistant with typo tolerance and per-message language detection
- **Admin Panel** — Filament resources for products, orders, bookings, customers, settings, and an audit log
- **Light / dark mode** — flash-free, cookie-driven
- **Security & a11y** — security headers, rate limiting, honeypot, WCAG-aligned accessibility

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13 (PHP 8.3+) |
| Reactive UI | Livewire 4 + Alpine.js |
| Admin Panel | Filament 5 |
| Styling / Build | Tailwind CSS v4 via Vite |
| 3D | Three.js |
| Database | SQLite (dev) / MySQL (production) |
| Media | Spatie MediaLibrary |

---

## Live Demo

- Storefront: [winwincaraudio.onrender.com](https://winwincaraudio.onrender.com)
- Admin panel: [winwincaraudio.onrender.com/admin](https://winwincaraudio.onrender.com/admin)

---

## Project Scope

This is a solo capstone project, built end-to-end (backend, frontend, admin panel, and deployment) to demonstrate a complete, production-shaped web application rather than an isolated coding exercise. It covers a full customer-facing storefront, a real booking/ordering workflow, a role-based admin panel, and the supporting concerns (security, accessibility, localization, observability) that a real small-business site would need.

The business itself — Win Win Car Studio — and all product data, images, and customer information are fictional, created for demonstration purposes only.

---

## License

Built for academic capstone submission. Not intended for commercial use.
