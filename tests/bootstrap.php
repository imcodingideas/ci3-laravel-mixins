<?php

use Dotenv\Dotenv;

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set up environment for testing
$_SERVER['CI_ENV'] = 'testing';
putenv('CI_ENV=testing');

// Define path constants
define('FCPATH', realpath(__DIR__ . '/../') . '/');
define('BASEPATH', realpath(__DIR__ . '/../system') . '/');
define('APPPATH', realpath(__DIR__ . '/../application') . '/');
define('VIEWPATH', APPPATH . 'views/');
define('ENVIRONMENT', 'testing');

// Load environment variables for database connection
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    // Set default values if .env not found
    $_ENV['DB_HOST'] = '127.0.0.1';
    $_ENV['DB_PORT'] = '3307';
    $_ENV['DB_DATABASE'] = 'codeigniter_db';
    $_ENV['DB_USERNAME'] = 'ci_user';
    $_ENV['DB_PASSWORD'] = 'ci_password';
    $_ENV['APP_URL'] = 'http://localhost:8000';
    $_ENV['APP_ENV'] = 'testing';
}

// Determine if we're running feature tests or unit tests
$backtrace = debug_backtrace();
$isFeatureTest = false;

foreach ($backtrace as $trace) {
    if (isset($trace['file']) && str_contains($trace['file'], '/feature/')) {
        $isFeatureTest = true;
        break;
    }
}

// For feature tests, we need the full CodeIgniter with routing
// For unit tests, we use the simplified version
if ($isFeatureTest) {
    // Set up for feature tests - we'll handle CodeIgniter loading in each test
    // Just define constants and environment here
} else {
    // Load simplified CodeIgniter for unit testing (no routing, no output)
    require_once __DIR__ . '/codeigniter_test.php';
}

// Load database helper functions for tests
if (!function_exists('reset_test_database')) {
    function reset_test_database()
    {
        $CI = &get_instance();

        // Disable foreign key checks to allow dropping tables
        $CI->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Drop all tables in correct order (children first)
        $CI->db->query('DROP TABLE IF EXISTS post_tags');
        $CI->db->query('DROP TABLE IF EXISTS tags');
        $CI->db->query('DROP TABLE IF EXISTS posts');
        $CI->db->query('DROP TABLE IF EXISTS authors');

        // Re-enable foreign key checks
        $CI->db->query('SET FOREIGN_KEY_CHECKS = 1');

        // Execute all SQL files to recreate database in correct order
        $sql_files = [
            __DIR__ . '/../database/init/01_create_posts_table.sql',
            __DIR__ . '/../database/init/02_create_authors_table.sql',
            __DIR__ . '/../database/init/03_create_tags_table.sql',
            __DIR__ . '/../database/init/04_update_posts_table.sql'
        ];

        foreach ($sql_files as $sql_file) {
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                
                // Split SQL statements by semicolon
                $statements = array_filter(array_map('trim', explode(';', $sql)));

                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $CI->db->query($statement);
                    }
                }
            }
        }
    }
} 
