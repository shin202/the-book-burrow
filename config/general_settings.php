<?php

return [
    'site' => [
        'title' => env('APP_NAME', 'Laravel'),
        'logo' => 'logo.svg',
        'email' => null,
        'phone' => '(84)-438 337 181',
        'address' => '93-95 Cau Giay Street, Cau Giay District, Hanoi, Vietnam',
        'socials' => [
            'facebook' => null,
            'twitter' => null,
            'instagram' => null,
            'youtube' => null,
        ],
        'recommendation' => [
            'url' => "https://api.bookburrow.dev/recommend",
            'limit' => 5
        ]
    ]
];
