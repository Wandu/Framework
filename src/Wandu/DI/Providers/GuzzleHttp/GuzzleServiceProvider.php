<?php
namespace Wandu\DI\Providers\GuzzleHttp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class GuzzleServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(ClientInterface::class, function (Config $config) {
            return new Client($config->get('guzzlehttp.guzzle', []));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
