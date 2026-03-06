<?php

return [

    /*
     * RF Moto — CORS Configuration
     * Allows the HTML frontend (localhost:80) to call the Laravel API (localhost:8000)
     * with session cookies (credentials: 'include')
     */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // ← Only allow your own frontend — never use '*' with credentials
    'allowed_origins' => [
        'http://localhost',
        'http://127.0.0.1',
        'http://localhost:80',
        'http://127.0.0.1:80',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ← MUST be true — required for session cookie to work across ports
    'supports_credentials' => true,

];