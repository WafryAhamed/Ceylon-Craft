# Ceylon Craft

A modern Laravel web application built with Vue.js and Tailwind CSS for managing products and users.

## Features

- **User Management** - User authentication and management system
- **Product Management** - Create, read, update, and delete products
- **Modern Stack** - Built with Laravel 12, Vue 3, and Tailwind CSS
- **API-First** - RESTful API endpoints for seamless integration
- **Testing** - Complete test suite with PHPUnit and Factory support

## Tech Stack

- **Backend**: Laravel 12.0
- **Frontend**: Vue 3 with Vite bundler
- **Styling**: Tailwind CSS 4.0
- **Database**: Configured for MySQL
- **PHP**: ^8.2
- **Testing**: PHPUnit 11.5.50

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- Laravel Sail (Docker) or local web server

### Installation

1. Clone the repository
```bash
git clone https://github.com/WafryAhamed/Ceylon-Craft.git
cd Ceylon-Craft
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node dependencies
```bash
npm install
```

4. Copy environment file and generate app key
```bash
cp .env.example .env
php artisan key:generate
```

5. Run database migrations
```bash
php artisan migrate
```

6. Seed the database (optional)
```bash
php artisan db:seed
```

### Development

Start the development server:

```bash
# Terminal 1: Start Laravel backend
php artisan serve

# Terminal 2: Build frontend assets with hot reload
npm run dev
```

Or use Laravel Sail:
```bash
./vendor/bin/sail up
```

### Building for Production

Build optimized frontend assets:
```bash
npm run build
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
│       ├── Product.php
│       └── User.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── js/
│   │   └── components/
│   ├── css/
│   └── views/
├── routes/
│   ├── api.php
│   ├── web.php
│   └── console.php
└── tests/
    ├── Feature/
    └── Unit/
```

## API Endpoints

The application provides RESTful API endpoints for managing resources. Refer to `routes/api.php` for complete endpoint documentation.

## Database

### Migrations

- `create_users_table` - User accounts and authentication
- `create_cache_table` - Cache storage
- `create_jobs_table` - Queue jobs
- `create_products_table` - Product inventory

### Seeders

- `ProductSeeder` - Sample product data
- `DatabaseSeeder` - Main database seeder

## Testing

Run the test suite:

```bash
php artisan test
```

Run tests with coverage:
```bash
php artisan test --coverage
```

## Configuration

Key configuration files:

- `config/app.php` - Application settings
- `config/database.php` - Database configuration
- `config/auth.php` - Authentication configuration
- `config/mail.php` - Mail service configuration

## Development Tools

- **Pint** - Code formatting and linting
- **Pail** - Log viewing
- **Tinker** - Interactive shell

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open source and available under the [MIT license](LICENSE).

## Author

- **Wafry Ahamed** - Initial work - [GitHub](https://github.com/WafryAhamed)

## Support

For support, email or open an issue on the GitHub repository.

---

**Ceylon Craft** - Bringing Sri Lankan craftsmanship to the digital world.
