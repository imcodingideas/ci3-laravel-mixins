# CodeIgniter 3 Blog Setup

This is a CodeIgniter 3.1.13 application with a Posts blog system, running with PHP 7.4 and MySQL 5.7 in Docker.

## Requirements

- PHP 7.4 (local installation)
- Docker and Docker Compose
- Composer (optional)

## Setup Instructions

### 1. Environment Configuration

Copy the environment file and configure your database settings:

```bash
cp env.example .env
```

Edit `.env` file if needed (default values should work):

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=codeigniter_db
DB_USERNAME=ci_user
DB_PASSWORD=ci_password
APP_URL=http://localhost
APP_ENV=development
```

### 2. Start Database Services

Start MySQL and phpMyAdmin with Docker:

```bash
docker-compose up -d
```

This will start:

- MySQL 5.7 on port 3306
- phpMyAdmin on port 8080

### 3. Database Setup

The database and sample data will be created automatically when you start the containers for the first time. The init script is located in `database/init/01_create_posts_table.sql`.

### 4. Run the Application

Since we're using PHP locally, start a development server:

```bash
# Navigate to your project directory
cd /path/to/your/codeigniter-project

# Start PHP development server
php -S localhost:8000
```

### 5. Access the Application

- **Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

## Features

### Posts Management

The application includes a complete CRUD system for blog posts:

- **List Posts**: View all posts at `/posts` or homepage
- **View Post**: View individual post at `/posts/view/{id}`
- **Create Post**: Create new post at `/posts/create`
- **Edit Post**: Edit existing post at `/posts/edit/{id}`
- **Delete Post**: Delete post at `/posts/delete/{id}`
- **API Endpoint**: Get posts as JSON at `/posts/api`

### Database Structure

The `posts` table includes:

- `id` (Primary Key)
- `title` (VARCHAR 255)
- `content` (TEXT)
- `author` (VARCHAR 100)
- `status` (ENUM: published, draft, archived)
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

## File Structure

```
application/
├── config/
│   ├── database.php      # Database configuration with env() functions
│   ├── autoload.php      # Auto-loads database and URL helper
│   └── dotenv.php        # Environment variables loader
├── controllers/
│   └── Posts.php         # Posts controller with CRUD operations
├── models/
│   └── Post_model.php    # Post model with database operations
└── views/
    ├── templates/
    │   ├── header.php    # Common header template
    │   └── footer.php    # Common footer template
    └── posts/
        ├── index.php     # Posts listing view
        └── create.php    # Create post form
```

## Docker Services

- **mysql**: MySQL 5.7 database server
- **phpmyadmin**: Web interface for MySQL management

## Stopping the Services

```bash
docker-compose down
```

To remove all data:

```bash
docker-compose down -v
```

## Troubleshooting

### Database Connection Issues

1. Make sure Docker containers are running: `docker-compose ps`
2. Check if ports 3306 and 8080 are available
3. Verify your `.env` file configuration
4. Ensure your local PHP has the `mysqli` extension enabled

### PHP Issues

1. Check PHP version: `php -v` (should be 7.4.x)
2. Verify mysqli extension: `php -m | grep mysqli`
3. Check if port 8000 is available for the development server

## Development Notes

This setup follows CodeIgniter 3 conventions:

- Models are suffixed with `_model`
- Controllers use PascalCase
- Database configuration uses environment variables via custom `env()` function
- URL helper is auto-loaded for `base_url()` function
- Database library is auto-loaded for all controllers
