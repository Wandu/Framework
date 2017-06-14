<?php
namespace Wandu\Event;

use PHPUnit\Framework\TestCase;
use Wandu\DI\Container;
use Wandu\Event\Contracts\Listener;
use Wandu\Event\Listener\ListenHandler;

class EventEmitterTest extends TestCase 
{
    public function testSimple()
    {
        $dispatcher = new EventEmitter();

        $dispatcher->on("hello", function () {
            static::assertEquals(['hello world'], func_get_args());
        });

        $dispatcher->trigger("hello", "hello world");
    }
    
    public function testClassEvent()
    {
        $dispatcher = new EventEmitter();

        $dispatcher->on(EventEmitterTestEvent::class, function () {
            static::assertEquals([new EventEmitterTestEvent("hello world in class!")], func_get_args());
        });

        $dispatcher->trigger(new EventEmitterTestEvent("hello world in class!"));
    }

    public function testClassListener()
    {
        $dispatcher = new EventEmitter();
        $dispatcher->setContainer(new Container());

        $dispatcher->on(EventEmitterTestEvent::class, EventEmitterTestListener::class);

        static::expectOutputString("hello world in class!");
        $dispatcher->trigger(new EventEmitterTestEvent("hello world in class!"));
    }

    public function testClassListenHandler()
    {
        $dispatcher = new EventEmitter();
        $dispatcher->setContainer(new Container());

        $dispatcher->on(EventEmitterTestEvent::class, EventEmitterTestListenHandler::class);

        static::expectOutputString("test class list handler");
        $dispatcher->trigger(new EventEmitterTestEvent("test class list handler"));
    }
}

class EventEmitterTestEvent
{
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}

class EventEmitterTestListener implements Listener
{
    public function call(array $arguments = [])
    {
        echo $arguments[0]->message;
    }
}

class EventEmitterTestListenHandler extends ListenHandler
{
    public function handle(EventEmitterTestEvent $event)
    {
        echo $event->message;
    }
}
