<?php
namespace Wandu\Event;

use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\DI\Container;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testTrigger()
    {
        $dispatcher = new Dispatcher(new Container());

        $dispatcher->on(StubEvent::class, StubListener::class);

        ob_start();
        $dispatcher->trigger(new StubEvent(30));
        $obBuffer = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('30', $obBuffer);
    }
}

class StubEvent implements EventInterface
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}

class StubListener implements ListenerInterface
{
    public function handle(StubEvent $event)
    {
        echo $event->getId();
    }
}
