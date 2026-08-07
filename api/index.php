<?php

// Prepare writable /tmp directories for Laravel serverless execution
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');

// Create SQLite database in /tmp if DB_CONNECTION is sqlite
$dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
if (!file_exists($dbPath) && (getenv('DB_CONNECTION') === 'sqlite' || !getenv('DB_CONNECTION'))) {
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sourceDb)) {
        @copy($sourceDb, $dbPath);
    } else {
        @touch($dbPath);
    }
}

// Forward Vercel request to Laravel index.php
require __DIR__ . '/../public/index.php';
