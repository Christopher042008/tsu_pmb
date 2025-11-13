<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // semua endpoint
    'allowed_methods' => ['*'], // semua metode
    'allowed_origins' => ['*'], // semua domain
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
