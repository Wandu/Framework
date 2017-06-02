<?php
namespace Wandu\Q\Queue;

use Mockery;
use PHPUnit\Framework\TestCase;
use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\JobInterface;
use Wandu\Q\Contracts\SerializerInterface;
use Wandu\Q\Queue;
use Wandu\Q\Serializer\PhpSerializer;

class QueueTest extends TestCase
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
        static::assertEquals("Something To Return", $queue->dequeue());
    }
    
    public function testQueueWithPhpSerializer()
    {
        $sendObject = new \stdClass();
        $sendObject->message = "stdClass Message";
        
        $returnJob = Mockery::mock(JobInterface::class);
        
        $serializer = new PhpSerializer();
        $adapter = Mockery::mock(AdapterInterface::class);
        $adapter->shouldReceive('enqueue')->once()
            ->with($serializer, $sendObject);
        $adapter->shouldReceive('dequeue')->once()
            ->with($serializer)->andReturn($returnJob);

        $queue = new Queue($serializer, $adapter);


        $queue->enqueue($sendObject);
        static::assertSame($returnJob, $queue->dequeue());
    }
}
