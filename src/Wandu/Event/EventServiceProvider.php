<?php
namespace Wandu\Event;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Event\Contracts\EventEmitter as EventEmitterContract;
use Wandu\Q\Worker;

class EventServiceProvider implements ServiceProviderInterface
{
    /** @var array */
    protected $listeners = [
    ];

    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(EventEmitter::class, function (ContainerInterface $container, Worker $worker) {
            $emitter = new EventEmitter($this->listeners);
            $emitter->setContainer($container);
            $emitter->setWorker($worker);
            return $emitter;
        });
        $app->alias(EventEmitterContract::class, EventEmitter::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
