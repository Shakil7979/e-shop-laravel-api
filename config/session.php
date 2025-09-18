<?php

use Illuminate\Support\Str;

return [

    'driver' => env('SESSION_DRIVER', 'database'),

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    'encrypt' => env('SESSION_ENCRYPT', false),

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION'),

    'table' => env('SESSION_TABLE', 'sessions'),

    'store' => env('SESSION_STORE'),

    'lottery' => [2, 100],

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel')).'_session'
    ),

    'path' => env('SESSION_PATH', '/'),

    // 👇 Cookie domain (SPA auth এর জন্য দরকার)
    'domain' => env('SESSION_DOMAIN', null), 

    // local dev এ false রাখো, production এ অবশ্যই true
    'secure' => env('SESSION_SECURE_COOKIE', false),  

    'http_only' => env('SESSION_HTTP_ONLY', true),

    // 👇 Sanctum SPA cross-origin এর জন্য 'none'
    'same_site' => env('SESSION_SAME_SITE', 'lax'), 

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
