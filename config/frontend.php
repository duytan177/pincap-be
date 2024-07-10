<?php
return [
    'web' => [
        'domain' => env('WEB_DOMAIN', 'http://localhost:3000'),
        'paths' => [
            'login' => '/login',
            'register' => '/register',
        ],
    ],
    'app' => [
        'domain' => env('APP_DOMAIN', 'http://localhost:19000'),
        'paths' => [
            'login' => '/login',
            'register' => '/register',
        ],
    ],
];
