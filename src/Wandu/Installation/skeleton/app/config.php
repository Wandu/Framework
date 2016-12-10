<?php
return [
    'env' => 'develop',
    'debug' => true,
    'timezone' => 'UTC',
    'database' => [
        'connections' => [
            'default' => [
                'driver'    => 'mysql',
                'host'      => WANDU_DB_HOST,
                'database'  => WANDU_DB_DBNAME,
                'username'  => WANDU_DB_USERNAME,
                'password'  => WANDU_DB_PASSWORD,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => WANDU_DB_PREFIX,
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
