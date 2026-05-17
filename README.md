
# CPB-NGI Pawnshop Management System

A web-based Pawnshop Management System built with Laravel 11, featuring pawn transactions, POS for forfeited items, customer management, and role-based access control.

---

## Prerequisites

Make sure you have the following installed on your machine before proceeding:

| Software       | Required Version | Download Link                                      |
|----------------|------------------|----------------------------------------------------|
| **PHP**        | >= 8.2           | Bundled with XAMPP                                  |
| **Composer**   | >= 2.x           | https://getcomposer.org/download/                   |
| **Node.js**    | >= 18.x          | https://nodejs.org/                                 |
| **npm**        | >= 9.x           | Bundled with Node.js                                |
| **MySQL**      | >= 5.7 / 8.x     | Bundled with XAMPP                                  |
| **XAMPP**      | Latest           | https://www.apachefriends.org/download.html         |

### Required PHP Extensions (enabled by default in XAMPP)

- `pdo_mysql`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `bcmath`
- `fileinfo`
- `gd` (for PDF receipt generation)

---

## Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/PROTOTYPE-CPB-NGI-PAWNSHOP.git
cd PROTOTYPE-CPB-NGI-PAWNSHOP/CPB-NGI-Pawnshop
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

> **Note:** The `.env.example` is pre-configured for a local XAMPP/MySQL setup with:
> - Database: `pawnshop`
> - Username: `root`
> - Password: *(empty)*
>
> Adjust these values in your `.env` if your MySQL credentials are different.

### 5. Create the Database

Open **phpMyAdmin** (http://localhost/phpmyadmin) or MySQL CLI and create the database:

```sql
CREATE DATABASE pawnshop;
```

### 6. Run Migrations & Seed Data

```bash
php artisan migrate --seed
```

This will create all tables and seed:
- **Admin account:** `admin@pawnshop.com` / `password`
- **Teller account:** `teller@pawnshop.com` / `password`
- Default item categories
- Philippine location data (regions, provinces, cities, barangays)

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Start the Server

```bash
php artisan serve
```

Visit: **http://127.0.0.1:8000**

---

## Quick Start (TL;DR)

```bash
# After cloning, run these commands in the CPB-NGI-Pawnshop directory:
composer install
npm install
cp .env.example .env
php artisan key:generate
# Create 'pawnshop' database in MySQL first, then:
php artisan migrate --seed
npm run build
php artisan serve
```

---

## Default Login Credentials

| Role     | Email                   | Password   |
|----------|-------------------------|------------|
| Admin    | admin@pawnshop.com      | password   |
| Teller   | teller@pawnshop.com     | password   |

> You can create Manager and Cashier accounts via the Admin panel after logging in.

---

## User Roles & Access

| Module               | Admin | Manager | Teller | Cashier |
|----------------------|:-----:|:-------:|:------:|:-------:|
| Dashboard            |  ✅   |   ✅    |   ✅   |   ✅    |
| Customers            |  ✅   |   ✅    |   ✅   |   ❌    |
| Pawn Transactions    |  ✅   |   ✅    |   ✅   |   ❌    |
| POS (Forfeited Items)|  ✅   |   ✅    |   ❌   |   ✅    |
| Items / Inventory    |  ✅   |   ✅    |   ❌   |   ❌    |
| User Management      |  ✅   |   ❌    |   ❌   |   ❌    |
| Audit Logs           |  ✅   |   ❌    |   ❌   |   ❌    |

---

## Tech Stack

- **Backend:** Laravel 11.51 (PHP 8.2)
- **Frontend:** Blade Templates, TailwindCSS 3, Alpine.js
- **Build Tool:** Vite 7
- **Database:** MySQL
- **PDF Generation:** DomPDF (via `barryvdh/laravel-dompdf`)
- **Auth:** Laravel Breeze

---

## Project Structure

```
CPB-NGI-Pawnshop/
├── app/
│   ├── Http/Controllers/     # All controllers
│   ├── Http/Middleware/       # Role middleware
│   └── Models/                # Eloquent models
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Default data seeders
├── resources/views/
│   ├── layouts/               # App layout & navigation
│   ├── auth/                  # Login & register pages
│   ├── customers/             # Customer CRUD views
│   ├── transactions/          # Transaction views
│   ├── pawn-wizard/           # Step-by-step pawn wizard
│   ├── pos/                   # Point of Sale module
│   └── items/                 # Inventory views
├── routes/
│   ├── web.php                # Main routes
│   └── auth.php               # Authentication routes
└── public/                    # Public assets & compiled files
```

---

## Troubleshooting

### "Class not found" or autoload errors
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Corrupted vendor directory
```bash
# Delete vendor and reinstall
rm -rf vendor
composer install
```

### Vite/CSS not loading
```bash
npm run build
# Or for development with hot-reload:
npm run dev
```

### Migration errors
Make sure the `pawnshop` database exists in MySQL before running `php artisan migrate`.

---

## License

This project is for academic/prototype purposes.
