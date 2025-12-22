<?php
return [
    'web' => [
        'domain' => env('WEB_DOMAIN', 'http://localhost:3000'),
        'paths' => [
            'login' => '/login',
            'register' => '/register',
            'forgot_password' => '/forgot-password',
            'instagram_sync' => '/instagram/sync'
        ],
    ],
    'app' => [
        'domain' => env('APP_DOMAIN', 'http://localhost:19000'),
        'paths' => [
            'login' => '/login',
            'register' => '/register',
            'forgot_password' => '/forgot-password',
        ],
    ],

    'paths' => [
        'media_detail' => env('MEDIA_DETAIL_URL', '/medias'),
        'user_detail' => env('USER_DETAUL_URL', '/profile')
    ]
];
