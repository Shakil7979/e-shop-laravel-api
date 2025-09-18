<?php


return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:4000'],

    'allowed_headers' => ['*'],

    'supports_credentials' => true,
];
