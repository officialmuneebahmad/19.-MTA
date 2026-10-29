<?php
/**
 * Multan Tech Accessories — Master Configuration Template
 * Copy this file to config.php or let install.php generate it automatically.
 */

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Multan Tech Accessories',
        'url' => 'https://multantech.store',
        'key' => 'base64:YOUR_SECRET_32_BYTE_APP_KEY_HERE',
        'env' => 'production', // 'production', 'staging', 'development'
        'debug' => false,
        'admin_path' => 'mta-control-9872', // Obfuscated admin path
    ],
    'db' => [
        'driver' => 'mysql', // 'mysql' or 'sqlite'
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'multantech',
        'username' => 'multan_user',
        'password' => 'YOUR_STRONG_DATABASE_PASSWORD',
        'sqlite_path' => __DIR__ . '/storage/database.sqlite',
        'charset' => 'utf8mb4',
    ],
    'supplier' => [
        'api_url' => 'https://api.computerzone.pk/products',
        'auth_header_format' => 'raw', // 'raw' (Authorization: <token>) or 'bearer' (Authorization: Bearer <token>)
        'token' => 'f1f3b0294c3f7c2328e41d42e59d2877',
        'timeout' => 120,
    ],
    'pricing' => [
        'default_percent' => 10,
        'default_fixed' => 0,
        'round_to' => 10,
    ],
    'delivery' => [
        'free_threshold' => 5000,
        'multan_charge' => 100,
        'standard_charge' => 250,
    ],
];
