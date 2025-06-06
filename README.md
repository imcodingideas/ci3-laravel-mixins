# CodeIgniter 3 Migration Test Project

This project is a **test case for migrating CodeIgniter 3 to the latest PHP version** (currently PHP 8.4). The objective is to identify risks and obstacles that could arise when attempting this migration in real applications, with the goal of addressing security vulnerabilities.

## Stack

- **Backend**: CodeIgniter 3.1.13 + 8.4
- **Database**: MySQL 5.7
- **Tools**: Docker, Composer, phpMyAdmin

## PHP Migration Status

- **Rector**: ✅ Full support for PHP 8.4 migration - Successfully completed
- **PHP CS Fixer**: ❌ **Do not use with PHP 8.4** - Currently unsupported

  **Important**: PHP CS Fixer does not support PHP 8.4 and should not be used. It may cause incorrect code modifications. Stick to Rector for PHP 8.4 compatibility.

## Quick Start

```bash
# Install dependencies
composer install

# Copy environment file
cp env.example .env

# Start database services
docker-compose up -d

# Start development server
composer dev

# Run tests
composer test
```

## Access

- **App**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

## Demo

Simple blog with posts CRUD operations at `/` route.

Database and sample data are created automatically on first run.
