<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Win Win Capstone Project (Powered by Laravel)

Welcome to the **Win Win Capstone Project**, a modern web application built for an auto accessories showroom. The platform allows customers to discover products, read testimonials, contact the store via WhatsApp, and enables administrators to manage the store cleanly using a powerful admin panel.

## 🚀 Features

- **Storefront (Customer Facing)**
  - Beautiful, responsive Homepage with hero sections, categories, and new arrivals.
  - Featured product highlights and full product catalog.
  - WhatsApp integration for seamless customer inquiries.
  - Real-time customer reviews and testimonials.
  - Multi-language support (English, Malay, Chinese) built-in.

- **Admin Dashboard (Filament)**
  - Complete Content Management System accessible at `/admin`.
  - **Products**: Manage catalog, set featured items, update pricing and inventory.
  - **Categories**: Organize products efficiently.
  - **Feedback**: Review and approve customer testimonials to be shown on the homepage.
  - **Contacts**: Manage customer inquiries submitted through the contact form.

## 🛠️ Tech Stack

- **Backend framework**: Laravel 13
- **Frontend / Fullstack**: Livewire v3
- **Styling**: Tailwind CSS
- **Admin Panel**: Filament v3 (v5.x)
- **Database**: SQLite / MySQL (Configurable via `.env`)

## 📦 Getting Started

### Prerequisites

Ensure you have the following installed on your local machine:
- PHP >= 8.3
- Composer
- Node.js & NPM

### Installation

1. **Clone the repository** (if applicable) and navigate to the project directory:
   ```bash
   cd capstone
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install NPM dependencies and build assets**:
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup**:
   Copy the example environment file and generate your application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Migration & Seeding**:
   Setup the database structure:
   ```bash
   php artisan migrate
   ```
   *(Optional)* If you have seeders ready, run `php artisan db:seed` to populate dummy data.

6. **Serve the Application**:
   Start the Laravel local development server:
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`.

### Accessing the Admin Panel

- Navigate to `http://localhost:8000/admin`.
- If you haven't created an admin user yet, you can create one using the artisan command:
  ```bash
  php artisan make:filament-user
  ```

## 💖 About Laravel

This project is proudly built on **Laravel**, a web application framework with expressive, elegant syntax. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:
- Simple, fast routing engine
- Expressive, intuitive database ORM
- Database agnostic schema migrations

Learn more about Laravel at [laravel.com](https://laravel.com/).

## 📝 License

This project is open-source and available under the [MIT license](https://opensource.org/licenses/MIT).
