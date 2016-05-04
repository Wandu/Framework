<?php
namespace Wandu\Event;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    /** @var array */
    protected $listeners = [
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
        $app->closure(DispatcherInterface::class, function ($container) {
            return new Dispatcher($container);
        });
    }
}
