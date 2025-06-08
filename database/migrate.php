<?php
/**
 * Database Migration Script
 * 
 * This script will execute all SQL migration files in the correct order
 * to set up the database with the new Author and Tag models.
 * 
 * Usage: php migrate.php
 */

// Load Composer autoloader
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Load environment variables using vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
$dotenv->load();

// Initialize Laravel Database
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

// Add database connection using environment variables
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOSTNAME'] ?? '127.0.0.1',
    'database'  => $_ENV['DB_DATABASE'] ?? 'codeigniter_blog',
    'username'  => $_ENV['DB_USERNAME'] ?? 'root',
    'password'  => $_ENV['DB_PASSWORD'] ?? '',
    'port'      => $_ENV['DB_PORT'] ?? 3306,
    'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8_general_ci',
    'prefix'    => $_ENV['DB_PREFIX'] ?? '',
]);

// Make this Capsule instance available globally
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

// Get database connection
$db = $capsule->getConnection();

// Helper function for current timestamp
if (!function_exists('now')) {
    function now() {
        return date('Y-m-d H:i:s');
    }
}

echo "=== CodeIgniter Blog Migration Script ===\n";
echo "Connecting to database: " . ($_ENV['DB_DATABASE'] ?? 'codeigniter_blog') . " on " . ($_ENV['DB_HOSTNAME'] ?? '127.0.0.1') . "\n\n";

try {
    // Test database connection
    $db->getPdo();
    echo "✓ Database connection successful\n\n";
    
    // Drop existing tables
    echo "🔄 Dropping existing tables...\n";
    $tables_to_drop = ['post_tags', 'posts', 'authors', 'tags'];
    foreach ($tables_to_drop as $table) {
        try {
            $db->statement("DROP TABLE IF EXISTS `$table`");
            echo "  ✓ Dropped table: $table\n";
        } catch (Exception $e) {
            echo "  ⚠ Could not drop table: $table\n";
        }
    }
    echo "\n";
    
    // Get migration files in order
    $migration_dir = dirname(__FILE__) . '/init/';
    $migration_files = [
        '01_create_posts_table.sql',
        '02_create_authors_table.sql', 
        '03_create_tags_table.sql',
        '04_update_posts_table.sql'
    ];
    
    echo "Migration files to process:\n";
    foreach ($migration_files as $file) {
        echo "  - $file\n";
    }
    echo "\n";
    
    foreach ($migration_files as $file) {
        $file_path = $migration_dir . $file;
        
        if (!file_exists($file_path)) {
            echo "⚠ Warning: Migration file not found: $file\n";
            continue;
        }
        
        echo "🔄 Executing $file...\n";
        
        try {
            // Read and execute SQL file
            $sql_content = file_get_contents($file_path);
            
            // Remove comments and split by semicolon
            $sql_content = preg_replace('/--.*$/m', '', $sql_content); // Remove comments
            $sql_content = preg_replace('/\s+/', ' ', $sql_content); // Normalize whitespace
            
            $statements = array_filter(
                array_map('trim', explode(';', $sql_content)),
                function($stmt) {
                    return !empty($stmt);
                }
            );
            
            foreach ($statements as $statement) {
                if (trim($statement)) {
                    $db->statement($statement);
                }
            }
            
            echo "  ✓ Success\n";
            
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            
            // Ask user if they want to continue
            echo "Do you want to continue with the next migration? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            fclose($handle);
            
            if (trim(strtolower($line)) !== 'y') {
                echo "Migration stopped by user.\n";
                exit(1);
            }
        }
    }
    
    echo "\n=== Migration Summary ===\n";
    
    echo "All migrations executed successfully!\n";
    
    // Verify tables exist
    echo "\nVerifying database structure:\n";
    
    $tables_to_check = ['posts', 'authors', 'tags', 'post_tags'];
    foreach ($tables_to_check as $table) {
        try {
            $count = $db->table($table)->count();
            echo "  ✓ Table '$table' exists with $count records\n";
        } catch (Exception $e) {
            echo "  ✗ Table '$table' does not exist\n";
        }
    }
    
    // Check for full-text indexes
    echo "\nVerifying search indexes:\n";
    try {
        $ft_indexes = $db->select("SHOW INDEX FROM posts WHERE Index_type = 'FULLTEXT'");
        
        if (count($ft_indexes) > 0) {
            echo "  ✓ Full-text search indexes found on posts table\n";
        } else {
            echo "  ⚠ No full-text indexes found on posts table\n";
        }
    } catch (Exception $e) {
        echo "  ⚠ Could not check full-text indexes\n";
    }
    
    echo "\n🎉 Migration completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Start your web server (docker-compose up -d)\n";
    echo "2. Visit your application at http://localhost\n";
    echo "3. Try the search functionality and tag/author filtering\n";
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check your .env file configuration\n";
    exit(1);
} 
