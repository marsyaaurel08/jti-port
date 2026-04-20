# JTI Port - PPI Student Management System

> A comprehensive web-based platform for managing PPI (Perhimpunan Pelajar Indonesia) student data, housing information, graduate tracking, and communications.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-4-09A4E4?style=flat-square&logo=filament)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4-06B6D4?style=flat-square&logo=tailwindcss)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Development](#development)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Core Functionality

- 👤 **Student Registration & Profiles** - Centralized student data management
- 🏠 **Housing Database** - Apartment listings with peer reviews and ratings
- 🎓 **Graduate Tracking** - Interactive global map of alumni locations
- 📰 **News & Bulletins** - Email broadcasting system for announcements
- 📊 **Analytics & Reporting** - Demographic insights and analytics
- 💳 **Course Payment** - BNI Virtual Account integration for secure transactions
- 🔐 **Role-Based Access Control** - Filament Shield integration for permissions
- 📧 **Multi-Channel Communication** - Email broadcasting and notifications

### Admin Features

- 📋 CRUD operations for all entities
- 📊 Advanced filtering and searching
- 📈 Dashboard widgets and analytics
- 👥 User management and role assignment
- 🔍 Activity logging and audit trails
- 📤 Import/Export functionality

## 🛠 Tech Stackss

| Component        | Version                     | Purpose                    |
| ---------------- | --------------------------- | -------------------------- |
| **PHP**          | 8.3.24                      | Backend Language           |
| **Laravel**      | 12                          | Framework                  |
| **Filament**     | 4                           | Admin Panel & UI Framework |
| **Livewire**     | 3                           | Real-time UI Components    |
| **Tailwind CSS** | 4                           | Styling                    |
| **Pest**         | 4                           | Testing Framework          |
| **Database**     | SQLite (dev) / MySQL (prod) | Data Storage               |
| **BNI API**      | Ecollection                 | Payment Gateway            |

## 📦 Prerequisites

- **PHP** 8.3 or higher
- **Composer** 2.0+
- **Node.js** 18+ & **npm** 9+
- **SQLite** or **MySQL** 8.0+
- **Git**

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/jti-port.git
cd jti-port
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

```bash
php artisan migrate --seed
```

### 5. Build Frontend Assets

```bash
npm run build
```

**Or use the quick setup command:**

```bash
composer setup
```

## ⚙️ Configuration

### Environment Variables

Key configurations in `.env`:

```env
APP_NAME="JTI Port"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite

# BNI Payment Gateway
BNI_CLIENT_ID=your_client_id
BNI_CLIENT_SECRET=your_client_secret
BNI_API_KEY=your_api_key
BNI_API_URL=https://app.sandbox.midtrans.com

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
```

### BNI Payment Gateway Setup

1. Register at BNI Ecollection Dashboard
2. Get credentials: `CLIENT_ID`, `CLIENT_SECRET`, `API_KEY`
3. Add to `.env` file
4. Test using sandbox environment first

## ▶️ Running the Application

### Development Server

```bash
composer run dev
```

This command starts:

- Laravel development server (port 8000)
- Vite dev server (port 5173)
- Queue worker
- Logs in real-time

### Individual Services

**Laravel Server:**

```bash
php artisan serve
```

**Vite Dev Server:**

```bash
npm run dev
```

**Queue Worker:**

```bash
php artisan queue:work
```

**View Logs:**

```bash
php artisan pail
```

## 🧪 Development

### Code Formatting

Format all code with Laravel Pint (required before commits):

```bash
vendor/bin/pint
```

### Running Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/ExampleTest.php

# Filter by test name
php artisan test --filter=testName
```

### Using Tinker (Interactive Shell)

```bash
php artisan tinker
```

### Database Debugging

```bash
php artisan tinker
>>> User::all();
>>> Order::with('course')->get();
```

## 📁 Project Structure

```
app/
├── Filament/              # Admin panel resources
│   ├── Resources/         # CRUD resources
│   ├── Exports/          # Export classes
│   ├── Imports/          # Import classes
│   └── Widgets/          # Dashboard widgets
├── Models/                # Eloquent models
├── Http/
│   ├── Controllers/       # API controllers
│   └── Requests/          # Form requests & validation
├── Services/              # Business logic
│   └── BNIService.php    # Payment gateway service
├── Listeners/             # Event listeners
├── Observers/             # Model observers
└── Policies/              # Authorization policies

database/
├── migrations/            # Schema migrations
├── factories/             # Model factories for testing
└── seeders/              # Database seeders

resources/
├── views/                 # Blade templates
├── css/                   # Tailwind stylesheets
└── js/                    # Alpine.js & Vite scripts

routes/
├── web.php               # Web routes
└── console.php           # Console commands

tests/
├── Feature/              # Feature tests
├── Unit/                 # Unit tests
└── Browser/              # Browser tests (Pest v4)
```

## 📚 Documentation

- **[PRD.md](PRD.md)** - Product Requirements Document
- **[CLAUDE.md](CLAUDE.md)** - Development Guide & Conventions
- **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - BNI Integration Details
- **[docs/filament-convention.md](docs/filament-convention.md)** - Filament Code Conventions

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. Create a feature branch: `git checkout -b feature/amazing-feature`
2. Commit changes: `git commit -m 'Add amazing feature'`
3. Push to branch: `git push origin feature/amazing-feature`
4. Open a Pull Request

### Code Standards

- Follow PSR-12 (enforced by Laravel Pint)
- Use type hints and explicit return types
- Always use curly braces for control structures
- Write tests for new features
- Update documentation as needed

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).
