# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Technology Stack

This is a **Laravel 12** application with **Filament 4** admin panel. Key technologies:
- **PHP**: 8.3.24
- **Laravel Framework**: v12
- **Filament**: v4 (Server-Driven UI framework)
- **Livewire**: v3
- **Tailwind CSS**: v4
- **Pest**: v4 (Testing framework)
- **Database**: SQLite (default)

## Development Commands

### Setup & Installation
```bash
composer setup          # Full setup: install, env, key, migrate, npm
composer run dev        # Start all services (server, queue, logs, vite)
```

### Testing
```bash
composer run test                         # Run all tests
php artisan test                          # Alternative test command
php artisan test tests/Feature/ExampleTest.php   # Run specific test file
php artisan test --filter=testName       # Run tests matching name
```

### Building & Formatting
```bash
npm run dev             # Start Vite dev server
npm run build           # Build frontend assets
vendor/bin/pint         # Format PHP code (required before commits)
```

### Database
```bash
php artisan migrate              # Run migrations
php artisan db:seed              # Seed database
php artisan migrate:fresh --seed # Fresh database with seeds
```

### Development Tools
```bash
php artisan tinker              # Interactive PHP shell
php artisan pail                # View logs in real-time
php artisan telescope           # Laravel Telescope dashboard
```

## Project Architecture

### Filament Resource Structure

This project follows a **structured Filament v4 resource pattern**. Resources are organized into subdirectories with separated concerns:

```
app/Filament/Resources/
├── Books/
│   ├── BookResource.php           # Main resource class
│   ├── Pages/                     # Resource pages
│   │   ├── CreateBook.php
│   │   ├── EditBook.php
│   │   └── ListBooks.php
│   ├── Schemas/                   # Form schemas
│   │   └── BookForm.php
│   └── Tables/                    # Table configurations
│       └── BooksTable.php
├── Users/
│   └── [same structure]
└── Activities/
    └── [same structure]
```

**Critical Pattern**: Each resource has:
- **Resource class**: Defines model, navigation, and coordinates form/table
- **Schemas/**: Form configurations (use `Filament\Schemas\Components` for layouts)
- **Tables/**: Table configurations with columns, filters, actions
- **Pages/**: Create, Edit, List pages

### Creating New Filament Resources

**ALWAYS use the Artisan command**:
```bash
php artisan make:filament-resource ModelName --generate --no-interaction
```

This creates the proper directory structure automatically.

### Filament v4 Key Differences

1. **Schemas namespace**: Layout components (`Section`, `Grid`, `Tabs`) are in `Filament\Schemas\Components`
2. **Unified Actions**: All actions use `Filament\Actions` namespace (no more `Filament\Tables\Actions`)
3. **Form fields**: Still in `Filament\Forms\Components`
4. **Deferred filters**: Filters require explicit apply button by default (use `deferFilters(false)` to disable)

Example from [BookForm.php](app/Filament/Resources/Books/Schemas/BookForm.php):
```php
use Filament\Schemas\Components\Section;  // Layout component
use Filament\Forms\Components\TextInput;  // Form component

Section::make('Book Information')
    ->schema([
        TextInput::make('title')
            ->required()
            ->maxLength(255),
    ])
```

### Authentication & Authorization

- **Filament Shield**: Role-based access control (RBAC)
- **Filament Breezy**: Two-factor authentication, profile management, Sanctum tokens
- **Social Login**: Google, GitHub, Facebook via Filament Socialite

### Application Features

Key installed packages:
- **spatie/laravel-activitylog**: Activity logging (scheduled cleanup daily)
- **spatie/laravel-medialibrary**: Media management
- **stechstudio/filament-impersonate**: User impersonation
- **Import/Export**: Custom importers/exporters in `app/Filament/Imports` and `app/Filament/Exports`

### Directory Structure

```
app/
├── Filament/
│   ├── Resources/          # Filament resources (structured pattern)
│   ├── Exports/            # Export classes
│   ├── Imports/            # Import classes
│   └── Widgets/            # Dashboard widgets
├── Models/                 # Eloquent models
├── Policies/               # Authorization policies
├── Providers/
│   └── Filament/
│       └── AdminPanelProvider.php  # Panel configuration
└── Http/
    └── Controllers/

bootstrap/
├── app.php                 # Application configuration & middleware
└── providers.php           # Service providers

routes/
├── web.php                 # Web routes
└── console.php             # Console commands

tests/
├── Feature/                # Feature tests
└── Unit/                   # Unit tests
```

### Laravel 12 Specifics

- **No `app/Http/Middleware/`**: Middleware registered in `bootstrap/app.php`
- **No `app/Console/Kernel.php`**: Commands auto-register, schedule in `bootstrap/app.php`
- **Constructor promotion**: Use PHP 8 syntax in `__construct()`
- **Model casts**: Prefer `casts()` method over `$casts` property

### Testing Guidelines

- Use **Pest** syntax for all tests
- Authenticate before testing Filament features
- Use Livewire testing helpers for Filament components:

```php
livewire(ListBooks::class)
    ->assertCanSeeTableRecords($books)
    ->searchTable($books->first()->title);

livewire(CreateBook::class)
    ->fillForm(['title' => 'Test', 'year' => 2024])
    ->call('create')
    ->assertNotified();
```

### Code Formatting

**REQUIRED before commits**:
```bash
vendor/bin/pint
```

This project uses Laravel Pint with strict conventions. All code must be formatted before committing.

### Important Conventions

1. **Explicit return types**: Always declare return types for methods
2. **Type hints**: Use appropriate PHP type hints for parameters
3. **Curly braces**: Always use braces for control structures (even single-line)
4. **Avoid `env()` outside config**: Use `config()` helper instead
5. **Named routes**: Prefer `route()` over hardcoded URLs
6. **Eloquent over raw queries**: Use models and relationships, avoid `DB::`
7. **Form Requests**: Create Form Request classes for validation, not inline validation

### Environment Variables

Default setup uses SQLite. The `.env.example` has configuration for MySQL (commented out). Key services configured:
- **Telescope**: Debugging tool (enabled in local)
- **Debugbar**: Debug toolbar (enabled in local)
- **Queue**: Configured for async jobs
- **Social auth**: Requires provider credentials (Google, GitHub, Facebook)

## Additional Documentation

- [Laravel Boost Guidelines](.github/copilot-instructions.md) - Comprehensive Laravel 12 + Filament 4 guidelines
- [Filament Conventions](docs/filament-convention.md) - Detailed Filament v4 patterns
- [Feature Plans](docs/plan/) - Implementation plans for major features
