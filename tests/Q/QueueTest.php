<?php
namespace Wandu\Q;

use PHPUnit\Framework\TestCase;
use Wandu\Q\Adapter\ArrayAdapter;
use Wandu\Q\Serializer\PhpSerializer;

class QueueTest extends TestCase
{
    public function testSimpleEnqueue()
    {
        $queue = new Queue(new ArrayAdapter());
        $queue->flush();
        
        $queue->send("Hello World");
        $queue->send(["message" => "Hello World"]);

        static::assertEquals("Hello World", $queue->receive()->read());
        static::assertEquals(["message" => "Hello World"], $queue->receive()->read());
    }

    public function testQueueWithPhpSerializer()
    {
        $sendObject = new \stdClass();
        $sendObject->message = "stdClass Message";
        
        $queue = new Queue(new ArrayAdapter(), new PhpSerializer());

        $queue->send($sendObject);

        static::assertEquals($sendObject, $queue->receive()->read());
    }
}
