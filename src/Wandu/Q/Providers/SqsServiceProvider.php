<?php
namespace Wandu\Q\Providers;

use Aws\Sqs\SqsClient;
use Pheanstalk\PheanstalkInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class SqsServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(PheanstalkInterface::class, function () {
            return new SqsClient([
                'version' => 'latest',
                'credentials' => [
                    'key' => config('aws.sqs.key'),
                    'secret' => config('aws.sqs.secret'),
                ],
                'region' => config('aws.sqs.region'),
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
