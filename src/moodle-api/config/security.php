<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */

    // IP Whitelist (optional)
    // Để trống = allow all IPs
    // Production: thêm IPs của chatbot server, mobile app server, etc.
    'allowed_ips' => array_filter(explode(',', env('ALLOWED_IPS', ''))),

    // Rate Limiting
    'rate_limit' => [
        'max_attempts' => env('RATE_LIMIT_MAX_ATTEMPTS', 100),
        'decay_minutes' => env('RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    // Request Logging
    'logging' => [
        'enabled' => env('SECURITY_LOGGING_ENABLED', true),
        'log_channel' => env('SECURITY_LOG_CHANNEL', 'api'),
    ],

    // Input Sanitization
    'sanitize_input' => env('SANITIZE_INPUT', true),

    // CORS Configuration
    'cors' => [
        'allowed_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', '*'))),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'X-API-Key', 'Authorization'],
    ],

];
