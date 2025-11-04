# Palmleaf Store - Copilot Instructions

## Project Summary

This is a Laravel-based e-commerce store for Palmleaf Creates. The project uses:
- **Backend**: Laravel 9 (PHP 8.0+) with Jetstream and Livewire
- **Frontend**: Tailwind CSS, Alpine.js, Laravel Mix
- **E-commerce**: Lunar PHP for e-commerce functionality
- **Payment Processing**: Stripe (via lunarphp/stripe) and PayPal
- **Database**: MySQL
- **Search**: Meilisearch

## Repository Structure

### Key Directories
- `app/` - Laravel application code
  - `Actions/` - Custom action classes
  - `Console/` - Artisan commands
  - `Http/` - Controllers, middleware, requests
  - `Models/` - Eloquent models
  - `Providers/` - Service providers
  - `PaymentTypes/` - Payment integration logic
- `resources/` - Frontend assets and views
  - `views/` - Blade templates
  - `css/` - Stylesheets
  - `js/` - JavaScript files
- `routes/` - Route definitions
- `database/` - Migrations, seeders, factories
- `tests/` - PHPUnit tests (Feature and Unit)
- `config/` - Configuration files
- `public/` - Public assets

## Build, Test, and Lint Instructions

### Initial Setup
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate
```

### Database Setup
The project requires MySQL with the following default configuration:
- Database: `palmleaf`
- User: `palmleaf`
- Password: `palmleaf`
- Host: `localhost`

### Building Assets
```bash
# Development build
npm run dev

# Production build
npm run prod

# Watch for changes (hot reload)
npm run hot
```

### Running Tests
```bash
# Run all tests
php artisan test

# Or using PHPUnit directly
./vendor/bin/phpunit

# Run specific test suites
./vendor/bin/phpunit --testsuite=Unit
./vendor/bin/phpunit --testsuite=Feature
```

### Development Server
```bash
# Start Laravel development server
php artisan serve

# In a separate terminal, run asset watcher
npm run hot
```

### Linting and Code Style
The project uses StyleCI for code style enforcement (configuration in `.styleci.yml`).
Follow PSR-12 coding standards for PHP code.

## Coding Principles

### PHP/Laravel Standards
- Follow PSR-12 coding standards
- Use strict typing where possible
- Follow Laravel best practices and conventions
- Use Eloquent ORM for database operations
- Leverage Laravel's built-in features (validation, authentication, etc.)

### Frontend Standards
- Use Tailwind CSS utility classes for styling
- Follow Alpine.js conventions for JavaScript interactions
- Keep Livewire components focused and single-purpose
- Maintain responsive design principles

### General Practices
- Write descriptive commit messages
- Keep methods and functions focused on a single responsibility
- Add PHPUnit tests for new features (Feature tests for user-facing functionality, Unit tests for business logic)
- Document complex business logic with comments
- Use type hints and return types in PHP code
- Follow the existing code structure and patterns

## Key Technologies and Packages

### Core Laravel Packages
- `laravel/framework` (^9.0) - Core framework
- `laravel/jetstream` (^2.17) - Authentication scaffolding
- `livewire/livewire` (^2.5) - Full-stack framework

### E-commerce
- `lunarphp/lunar` (^0.1) - E-commerce functionality
- `lunarphp/stripe` (^0.1) - Stripe payment integration
- `srmklive/paypal` (~3.0) - PayPal integration

### Development Tools
- `spatie/laravel-ignition` - Error page for Laravel
- `itsgoingd/clockwork` - Debugging and profiling

## Environment Variables

Key environment variables that need to be configured:
- Database connection settings (`DB_*`)
- Application key (`APP_KEY`)
- Mail settings for notifications
- Payment gateway credentials (Stripe, PayPal)
- Storage configuration (S3 if using cloud storage)
- Meilisearch connection details

## Common Tasks

### Adding a New Feature
1. Create necessary migrations in `database/migrations/`
2. Add/update models in `app/Models/`
3. Create controllers in `app/Http/Controllers/`
4. Add routes in `routes/web.php` or `routes/api.php`
5. Create views in `resources/views/`
6. Write tests in `tests/Feature/` or `tests/Unit/`
7. Run tests to ensure functionality works

### Working with Payments
Payment logic is in `app/PaymentTypes/`. Follow the existing patterns when adding new payment methods or modifying payment processing.

### Database Changes
Always create migrations for database changes. Never modify the database directly in production.

## Testing Philosophy

- Feature tests should test user-facing functionality end-to-end
- Unit tests should test individual methods and classes in isolation
- Mock external services (payment gateways, email) in tests
- Maintain test database separation from development database
