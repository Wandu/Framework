<?php
namespace Wandu\Bridges\Monolog;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation;
use function Wandu\Foundation\config;

class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->closure(Logger::class, function (ContainerInterface $app) {
            $logger = new Logger('wandu');
            if ($path = config('log.path')) {
                $logger->pushHandler(new StreamHandler(
                    Foundation\path($path)
                ));
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
