<?php
namespace Wandu\DateTime;

use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class DateTimeServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        if ($config = $app->get(Config::class)) {
            date_default_timezone_set($config->get('timezone', 'UTC'));
        }
    }
}
