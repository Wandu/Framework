<?php
namespace Wandu\Event;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Event\Events\Ping;
use Wandu\Event\Listeners\Pong;

class EventServiceProvider implements ServiceProviderInterface
{
    /** @var array */
    protected $listeners = [
        Ping::class => [
            Pong::class,
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $app->get(DispatcherInterface::class)->setListeners($this->listeners);
    }

    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Dispatcher::class, function ($container) {
            return new Dispatcher($container);
        });
        $app->alias(DispatcherInterface::class, Dispatcher::class);
        $app->alias('event', Dispatcher::class);
    }
}
