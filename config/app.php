<?php
/**
 * Application Configuration
 */
return [
    'name'     => env('APP_NAME', 'ASR FORM'),
    'url'      => env('APP_URL', 'http://localhost'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => env('APP_DEBUG', false),
    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
    
    'session' => [
        'lifetime' => (int) env('SESSION_LIFETIME', 120),
        'name'     => env('SESSION_NAME', 'asr_form_session'),
    ],
];
