<?php
namespace Wandu\Q\Providers;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class BeanstalkServiceProvider implements ServiceProviderInterface 
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(PheanstalkInterface::class, function () {
            return new Pheanstalk(
                config('pda.pheanstalk.host', '127.0.0.1'),
                config('pda.pheanstalk.port', Pheanstalk::DEFAULT_PORT),
                config('pda.pheanstalk.timeout'),
                config('pda.pheanstalk.connect_persistent', false)
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
