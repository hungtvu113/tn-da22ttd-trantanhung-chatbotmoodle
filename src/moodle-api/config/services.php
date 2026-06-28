<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'moodle' => [
        'url' => env('MOODLE_URL', 'http://localhost/moodle'),
        'token' => env('MOODLE_TOKEN'),
        // Nguồn dữ liệu: 'db' = truy vấn CSDL trực tiếp | 'ws' = Moodle Web Services API.
        'data_source' => env('MOODLE_DATA_SOURCE', 'db'),
    ],

    // API Keys phan quyen theo vai tro (student/teacher/admin).
    // Moi vai tro 1 key rieng, gui qua header X-API-Key.
    // Phan cap: admin > teacher > student (key cao hon dung duoc endpoint thap hon).
    'api_keys' => [
        'student' => array_filter(explode(',', env('STUDENT_API_KEYS', ''))),
        'teacher' => array_filter(explode(',', env('TEACHER_API_KEYS', ''))),
        'admin'   => array_filter(explode(',', env('ADMIN_API_KEYS', ''))),
        // Key cu (chatbot) -> coi nhu admin de khong gay gian doan.
        'legacy'  => array_filter(explode(',', env('EXTERNAL_API_KEYS', ''))),
    ],

];