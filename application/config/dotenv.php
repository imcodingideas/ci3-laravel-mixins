<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
$dotenv->load();

/**
 * Get environment variable with fallback
 */
function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    
    if ($value === false || $value === null) {
        return $default;
    }
    
    // Handle boolean and null strings
    switch (strtolower(trim($value))) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }
    
    return $value;
} 
