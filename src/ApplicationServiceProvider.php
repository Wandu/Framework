<?php
namespace Wandu\App;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation\Contracts\HttpErrorHandlerInterface;
use Wandu\Foundation\Contracts\KernelInterface;
use Wandu\Foundation\Error\DefaultHttpErrorHandler;

class ApplicationServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->bind(HttpErrorHandlerInterface::class, DefaultHttpErrorHandler::class);
    }

    public function boot(ContainerInterface $app)
    {
        if ($app->has(KernelInterface::class)) {
            $kernel = $app->get(KernelInterface::class);
            $kernel['commands'] = require __DIR__ . '/../app/commands.php';
            $kernel['routes'] = require __DIR__ . '/../app/routes.php';
        }
    }
}
