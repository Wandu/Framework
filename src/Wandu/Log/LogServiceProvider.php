<?php
namespace Wandu\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->closure(Logger::class, function (ContainerInterface $app) {
            $logger = new Logger('wandu');
            if ($app['config']->get('log.path')) {
                $logger->pushHandler(new StreamHandler($app['config']->get('log.path')));
            }
            return $logger;
        });
        $app->alias(LoggerInterface::class, Logger::class);
        $app->alias('log', Logger::class);
    }

    public function boot(ContainerInterface $app)
    {
    }
}
