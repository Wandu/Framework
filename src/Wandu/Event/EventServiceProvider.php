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
        $dispatcher = $app->get(DispatcherInterface::class);
        foreach ($this->listeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->on($event, $listener);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Dispatcher::class, function (ContainerInterface $container) {
            return new Dispatcher($container);
        });
        $app->alias(DispatcherInterface::class, Dispatcher::class);
        $app->alias('event', Dispatcher::class);
    }
}
