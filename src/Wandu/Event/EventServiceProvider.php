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
    public function register(ContainerInterface $app)
    {
        $app->bind(DispatcherInterface::class, Dispatcher::class);
        $app->extend(Dispatcher::class, function (DispatcherInterface $dispatcher) {
            foreach ($this->listeners as $event => $listeners) {
                foreach ($listeners as $listener) {
                    $dispatcher->on($event, $listener);
                }
            }
            return $dispatcher;
        });
        $app->alias('event', Dispatcher::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
