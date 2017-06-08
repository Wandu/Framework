<?php
namespace Wandu\Q;

use Aws\Sqs\SqsClient;
use Pheanstalk\PheanstalkInterface;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Q\Adapter\BeanstalkdAdapter;
use Wandu\Q\Adapter\NullAdapter;
use Wandu\Q\Adapter\SqsAdapter;
use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;
use Wandu\Q\Serializer\JsonSerializer;
use Wandu\Q\Serializer\PhpSerializer;

class QueueServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(SerializerInterface::class, function (ConfigInterface $config) {
            switch ($config->get('q.serializer')) {
                case 'json':
                    return new JsonSerializer();
            }
            return new PhpSerializer();
        });
        $app->closure(AdapterInterface::class, function (ContainerInterface $app, ConfigInterface $config) {
            switch ($config->get('q.type')) {
                case 'beanstalkd':
                    $app->assert(PheanstalkInterface::class, 'pda/pheanstalk');
                    return new BeanstalkdAdapter($app->get(PheanstalkInterface::class));
                case 'sqs':
                    $app->assert(SqsClient::class, 'aws/aws-sdk-php');
                    return new SqsAdapter($app->get(SqsClient::class), $config->get('q.sqs.url'));
            }
            return new NullAdapter();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
