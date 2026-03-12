<?php

return [

    /*
     * RF Moto — CORS Configuration
     * Allows the HTML frontend (localhost:80) to call the Laravel API (localhost:8000)
     * with session cookies (credentials: 'include')
     */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://127.0.0.1:8000', 'http://localhost:8000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ← MUST be true — required for session cookie to work across ports
    'supports_credentials' => true,

];