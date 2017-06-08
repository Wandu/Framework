<?php
namespace Wandu\Q\Providers;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class BeanstalkServiceProvider implements ServiceProviderInterface 
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(PheanstalkInterface::class, function (Config $config) {
            return new Pheanstalk(
                $config->get('pda.pheanstalk.host', '127.0.0.1'),
                $config->get('pda.pheanstalk.port', Pheanstalk::DEFAULT_PORT),
                $config->get('pda.pheanstalk.timeout'),
                $config->get('pda.pheanstalk.connect_persistent', false)
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
