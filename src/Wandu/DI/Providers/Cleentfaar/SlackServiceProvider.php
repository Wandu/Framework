<?php
namespace Wandu\DI\Providers\Cleentfaar;

use CL\Slack\Transport\ApiClient;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class SlackServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(ApiClient::class, function () {
            return new ApiClient(
                config('cleentfaar.slack.token')
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
