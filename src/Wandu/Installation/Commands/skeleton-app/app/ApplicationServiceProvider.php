<?php
namespace ___NAMESPACE___;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation\Contracts\HttpErrorHandlerInterface;
use Wandu\Foundation\Error\DefaultHttpErrorHandler;

class ApplicationServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->bind(HttpErrorHandlerInterface::class, DefaultHttpErrorHandler::class);
    }

    public function boot(ContainerInterface $app)
    {
    }
}
