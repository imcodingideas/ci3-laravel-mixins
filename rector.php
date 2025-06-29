<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector;
use Rector\Transform\Rector\Class_\AddAllowDynamicPropertiesAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    // Define paths to refactor - including application code, tests AND entire CodeIgniter system
    $rectorConfig->paths([
        __DIR__ . '/application/controllers',
        __DIR__ . '/application/models',
        __DIR__ . '/application/libraries',
        __DIR__ . '/application/helpers',
        __DIR__ . '/application/core',
        __DIR__ . '/application/hooks',
        __DIR__ . '/application/views',
        __DIR__ . '/system', // Entire CodeIgniter system directory
        __DIR__ . '/tests',
    ]);

    // Skip certain paths that shouldn't be modified
    $rectorConfig->skip([
        __DIR__ . '/vendor', // Third-party packages
        __DIR__ . '/application/cache',
        __DIR__ . '/application/logs',
        __DIR__ . '/application/language',
        __DIR__ . '/system/fonts', // Font files
        __DIR__ . '/system/language', // Language files
        '*/index.html', // Default CodeIgniter index.html files
        
        // Skip specific rules for problematic files
        AddAllowDynamicPropertiesAttributeRector::class => [
            __DIR__ . '/system/database/DB.php', // Dynamic class definitions cause issues
        ],
    ]);

    // PHP 8.3 to PHP 8.4 migration rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
    ]);

    // Optional: Add specific rules for code quality
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ]);

    // Configure file extensions to process
    $rectorConfig->fileExtensions(['php']);

    // Optional: Configure parallel processing
    $rectorConfig->parallel();

    // Optional: Import short classes - useful for CodeIgniter
    $rectorConfig->importShortClasses(false);
    $rectorConfig->importNames();

    // Boot configuration for CodeIgniter environment
    $rectorConfig->bootstrapFiles([
        __DIR__ . '/tests/bootstrap.php',
    ]);

    // Apply early return pattern when possible
    $rectorConfig->rule(ChangeNestedIfsToEarlyReturnRector::class);

	// Add allow dynamic properties attribute to classes that have dynamic properties
	$rectorConfig->rule(AddAllowDynamicPropertiesAttributeRector::class);
};
