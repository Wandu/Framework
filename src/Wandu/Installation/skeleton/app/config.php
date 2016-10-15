<?php
return [
    'env' => 'develop',
    'debug' => true,
    'timezone' => 'UTC',
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
        'migrator' => [
            'connection' => 'default',
            'table' => 'migrations',
            'path' => 'migrations',
        ],
    ],
    'session' => [
        'type' => 'file',
        'path' => 'cache/sessions',
    ],
    'log' => [
        'path' => null,
    ],
    'view' => [
        'path' => 'views',
        'cache' => 'cache/views',
    ],
];
