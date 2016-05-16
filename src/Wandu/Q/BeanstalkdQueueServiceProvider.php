<?php
namespace Wandu\Q;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Q\Adapter\BeanstalkdAdapter;
use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;
use Wandu\Q\Serializer\PhpSerializer;

class BeanstalkdQueueServiceProvider implements ServiceProviderInterface 
{
    public function register(ContainerInterface $app)
    {
        $app->closure(PheanstalkInterface::class, function ($app) {
            return new Pheanstalk(
                $app['config']->get('queue.host', '127.0.0.1'),
                $app['config']->get('queue.port', Pheanstalk::DEFAULT_PORT),
                $app['config']->get('queue.timeout'),
                $app['config']->get('queue.connect_persistent', false)
            );
        });
        $app->bind(SerializerInterface::class, PhpSerializer::class);
        $app->bind(AdapterInterface::class, BeanstalkdAdapter::class);
    }

    public function boot(ContainerInterface $app)
    {
    }
}
