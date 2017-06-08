<?php
namespace Wandu\Q\Providers;

use Aws\Sqs\SqsClient;
use Pheanstalk\PheanstalkInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class SqsServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(PheanstalkInterface::class, function (Config $config) {
            return new SqsClient([
                'version' => 'latest',
                'credentials' => [
                    'key' => $config->get('aws.sqs.key'),
                    'secret' => $config->get('aws.sqs.secret'),
                ],
                'region' => $config->get('aws.sqs.region'),
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
