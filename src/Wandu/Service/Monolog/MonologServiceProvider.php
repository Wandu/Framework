<?php
namespace Wandu\Service\Monolog;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProvider;

class MonologServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(LoggerInterface::class, Logger::class)
            ->assign('name', ['value' => 'wandu'])
            ->after(function (Logger $logger, Config $config) {
                if ($path = $config->get('log.path')) {
                    $logger->pushHandler(new StreamHandler($path));
                }
                return $logger;
            });
    }
}
