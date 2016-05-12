<?php
namespace Wandu\Q\Queue;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;
use Wandu\Q\Queue;

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
