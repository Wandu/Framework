<?php
use Wandu\Console\Controllers\HelloWorld;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\ConfigInterface;
use Wandu\Router\Router;

return new class implements ConfigInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function register(ContainerInterface $app)
    {
    }

    public function routes(Router $router)
    {
        // TODO: Implement routes() method.
    }

    public function commands(Dispatcher $dispatcher)
    {
        $dispatcher->command('hello', HelloWorld::class);
    }
};
