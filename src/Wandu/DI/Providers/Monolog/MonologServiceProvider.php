<?php
namespace Wandu\DI\Providers\Monolog;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class MonologServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Logger::class, function () {
            $logger = new Logger('wandu');
            if ($path = config('log.path')) {
                $logger->pushHandler(new StreamHandler($path));
            }
            return $logger;
        });
        $app->alias(LoggerInterface::class, Logger::class);
        $app->alias('log', Logger::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
