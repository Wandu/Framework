<?php
namespace Wandu\Service\GuzzleHttp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProvider;

class GuzzleServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(Client::class, function (Config $config) {
            return new Client($config->get('guzzlehttp.guzzle', []));
        });
        $app->alias(ClientInterface::class, Client::class);
    }
}
