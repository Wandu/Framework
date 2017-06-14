<?php
namespace Wandu\Q\Adapter;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** @var \Wandu\Q\Contracts\Adapter */
    protected $queue;
    
    public function testQueue()
    {
        $this->queue->flush();
        
        static::assertNull($this->queue->receive());
        for ($i = 0; $i < 10; $i++) {
            $this->queue->send("hello {$i}");
        }

        for ($i = 0; $i < 10; $i++) {
            $job = $this->queue->receive();
            static::assertEquals("hello {$i}", $job->payload());
            $this->queue->remove($job);
        }

        static::assertNull($this->queue->receive());
    }

    public function testFlush()
    {
        $this->queue->flush();

        static::assertNull($this->queue->receive());
        for ($i = 0; $i < 10; $i++) {
            $this->queue->send("hello {$i}");
        }

        $this->queue->flush();
        static::assertNull($this->queue->receive());
    }
}
