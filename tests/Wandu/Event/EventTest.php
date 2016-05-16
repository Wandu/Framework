<?php
namespace Wandu\Event;

use Mockery;
use PHPUnit_Framework_TestCase;

class EventTest extends PHPUnit_Framework_TestCase
{
    public function testGetEventName()
    {
        $event = new EventTestStub();
        $this->assertEquals(EventTestStub::class, $event->getEventName());
    }
}

class EventTestStub extends Event
{
}
