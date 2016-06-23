<?php
return [
    'env' => 'develop',
    'debug' => true,
    'database' => [
        'connections' => [
            'default' => [
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'database'  => 'wandu',
                'username'  => 'root',
                'password'  => 'root',
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => 'local_',
            ],
        ],
        'migration' => [
            'path' => '%%path%%migrations',
        ],
    ],
    'log' => [
        'path' => null,
    ],
    'view' => [
        'path' => '%%path%%views',
        'cache' => '%%path%%cache/views',
    ],
];
