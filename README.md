# Fablead ERP System

A Laravel-based inventory and billing system for managing sales, purchases, stock, customers, vendors, invoices, payments, and reports.

## Features

- Sales, purchase, and inventory management
- Customer and vendor records
- Invoice and bill generation
- Cashbook, bankbook, and payment history
- GST, sales, purchase, and profit reports
- Role-based user access
- Excel and PDF exports
- Responsive layout for desktop, tablet, and mobile

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL
- Laravel supported web server environment

## Installation

1. Clone the project:

   ```bash
   git clone https://github.com/rajsingh10/inventory-billing.git
   cd inventory-billing
   ```

2. Install PHP and frontend dependencies:

   ```bash
   composer install
   npm install
   ```

3. Create the environment file:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Update `.env` with your database details:

   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

5. Set the application URL:

   ```env
   APP_URL=http://127.0.0.1:8000
   ImagePath=http://127.0.0.1:8000/
   ```

   Keep `ImagePath` ending with `/`.

6. Run migrations, Passport setup, and seeders:

   ```bash
   php artisan migrate
   php artisan passport:install --force
   php artisan db:seed
   ```

7. Create the storage link:

   ```bash
   php artisan storage:link
   ```

8. Start the application:

   ```bash
   php artisan serve
   ```

   Open `http://127.0.0.1:8000` in your browser.

## Default Login

Use these credentials after seeding the database:

```text
Email: admin@gmail.com
Password: 12345678
```

Change the default password after your first login.

## Common Commands

```bash
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

For a fresh local database setup:

```bash
php artisan migrate:fresh --seed
php artisan passport:install --force
```

## Troubleshooting

- If the page has no styling, check `APP_URL` and `ImagePath` in `.env`.
- If login or save actions fail, run `php artisan passport:install --force` and log in again.
- Use only one local URL while testing, such as `127.0.0.1` or `localhost`.
- If cached settings cause issues, run the clear commands listed above.

## License

This project is proprietary software developed by **Fablead Developers Technolab**. All rights reserved.
