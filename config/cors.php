<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_merge(
        ['https://www.tobias-hopp.de', 'https://*.tobias-hopp.de'],
        array_map('trim', explode(',', config('app.allowed_password_reset_urls', '')))
    ),
    'allowed_origins_patterns' => ['#^https?://(localhost|127\\.0\\.0\\.1)(:\\d+)?$#'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
