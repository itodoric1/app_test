<?php

declare(strict_types=1);

return [
    'name' => 'Finasport',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'fs_fsport_form',
        'user' => $_ENV['DB_USER'] ?? 'fsuser',
        'pass' => $_ENV['DB_PASS'] ?? 'SportaskaEkipa1',
        'charset' => 'utf8mb4',
    ],
];

