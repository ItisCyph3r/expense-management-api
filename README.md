# Expense Management API

A robust REST API built with Laravel for managing personal and business expenses, with features for tracking, categorizing, and reporting expenses.

## Features

- User authentication and authorization
- Expense tracking and management 
- Expense categories and tags
- Expense reports and analytics
- File attachments for receipts
- Role-based access control
- API documentation

## Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Laravel 10.x

## Installation

1. Clone the repository:
```bash
git clone https://github.com/itiscyph3r/expense-management-api.git
cd expense-management-api
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_management
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

6. Start the development server:
```bash
php artisan serve
```

## API Documentation

API documentation is available at `/api/documentation` when running the application.

### Available Endpoints

- `POST /api/auth/login` - User authentication
- `POST /api/auth/register` - User registration
- `GET /api/expenses` - List all expenses
- `POST /api/expenses` - Create new expense
- `GET /api/expenses/{id}` - Get expense details
- `PUT /api/expenses/{id}` - Update expense
- `DELETE /api/expenses/{id}` - Delete expense
- `GET /api/categories` - List expense categories
- `POST /api/reports` - Generate expense reports

## Testing

Run the test suite:
```bash
php artisan test
```

## Security

For vulnerability reports, please email [samuelmomoh61@gmail.com](mailto:samuelmomoh61@gmail.com)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

Built with [Laravel](https://laravel.com/) by [Your Name]

## Support

For support, email [samuelmomoh61@gmail.com](mailto:samuelmomoh61@gmail.com) or create an issue in the repository.
