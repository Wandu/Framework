<?php
namespace Wandu\DateTime;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

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
        date_default_timezone_set(config('timezone', 'UTC'));
    }
}
