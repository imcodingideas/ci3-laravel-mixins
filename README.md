# CodeIgniter 3 Blog Demo

## Stack

- **Backend**: CodeIgniter 3.1.13 + PHP 7.4
- **Database**: MySQL 5.7
- **Tools**: Docker, Composer, phpMyAdmin

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
