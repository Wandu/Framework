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
        $app->closure(Dispatcher::class, function (ContainerInterface $container) {
            $dispatcher = new Dispatcher($container);
            foreach ($this->listeners as $event => $listeners) {
                foreach ($listeners as $listener) {
                    $dispatcher->on($event, $listener);
                }
            }
            return $dispatcher;
        });
        $app->alias(DispatcherInterface::class, Dispatcher::class);
        $app->alias('event', Dispatcher::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
