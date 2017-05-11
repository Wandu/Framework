<?php
return [
    'host' => 'wandu.github.io',
    'env' => 'develop',
    'debug' => true,
    'timezone' => 'UTC',
    'caster' => [
        'casters' => [
            'datetime' => Wandu\Caster\Caster\CarbonCaster::class,
            'date' => Wandu\Caster\Caster\DateCaster::class,
            'time' => Wandu\Caster\Caster\TimeCaster::class,
        ],
    ],
    'database' => [
        'connections' => [
            'default' => [
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'database'  => 'allbus',
                'username'  => 'root',
                'password'  => '',
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
    'http' => [
        'session' => [
            'type' => 'file',
            'path' => 'cache/sessions',
        ],
    ],
    'view' => [
        'path' => 'views',
        'cache' => 'cache/views',
    ],
    
    // for other service providers :-)
    'monolog' => [
        'monolog' => [
            'path' => 'storage/log/wandu.log',
        ],
    ],
    'neomerx' => [
        'cors-psr7' => [
            'server-origin' => 'http://wandu.github.io', // == 'http://' . config('host')
            'allowed-origins' => [],
            'allowed-methods' => ['GET' => true, 'POST' => true, 'PUT' => true, 'DELETE' => true, 'OPTIONS' => true],
            'allowed-headers' => [],
        ],
    ],
];
