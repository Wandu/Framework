<?php
namespace Wandu\DI\Providers\Monolog;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class MonologServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Logger::class, function (ConfigInterface $config) {
            $logger = new Logger('wandu');
            if ($path = $config->get('log.path')) {
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
