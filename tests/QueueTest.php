<?php
namespace Wandu\Queue\Queue;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Queue\Contracts\AdapterInterface;
use Wandu\Queue\Contracts\SerializerInterface;
use Wandu\Queue\Queue;

class QueueTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testSimpleEnqueue()
    {
        $serializer = Mockery::mock(SerializerInterface::class);

        $adapter = Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('enqueue')->once()
            ->with($serializer, "Hello World");
        $adapter->shouldReceive('dequeue')->once()
            ->with($serializer)->andReturn("Something To Return");

        $queue = new Queue($serializer, $adapter);

        $queue->enqueue("Hello World");
        $this->assertEquals("Something To Return", $queue->dequeue());
    }
}
