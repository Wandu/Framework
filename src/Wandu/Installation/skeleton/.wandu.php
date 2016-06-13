<?php
use Wandu\Foundation\Definitions\FullDefinition;
use Wandu\Router\Controllers\HelloWorldController;
use Wandu\Router\Router;

return new class extends FullDefinition
{
    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return [
            'env' => 'develop',
            'debug' => true,
            'database' => [
                'connections' => [
                    'default' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'database'  => 'allbus',
                        'username'  => 'root',
                        'password'  => 'root',
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix'    => 'local_',
                    ],
                ],
                'migration' => [
                    'path' => 'migrations',
                ],
            ],
            'log' => [
                'path' => null,
            ],
            'view' => [
                'path' => 'views',
                'cache' => 'cache/views',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
        $router->get('/', HelloWorldController::class);
    }
};
