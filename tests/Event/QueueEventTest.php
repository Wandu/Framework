<?php
namespace Wandu\Event;

use Pheanstalk\Exception\ConnectionException;
use Pheanstalk\Pheanstalk;
use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\Event\Commands\Events\NormalPing;
use Wandu\Event\Commands\Events\QueuePing;
use Wandu\Event\Commands\Listeners\NormalPong;
use Wandu\Event\Commands\Listeners\QueuePong;
use Wandu\Q\Adapter\BeanstalkdAdapter;
use Wandu\Q\Queue;
use Wandu\Q\Serializer\PhpSerializer;
use Wandu\Q\Worker;
use Wandu\Q\WorkerStopper;

class QueueEventTest extends TestCase 
{
    use Assertions;
    
    /** @var \Wandu\Q\Queue */
    protected $queue;
    
    /** @var \Wandu\DI\Container */
    protected $container;

    /** @var \Wandu\Q\Worker */
    protected $worker;
    
    public function setUp()
    {
        try {
            $this->queue = new Queue(new BeanstalkdAdapter(new Pheanstalk('127.0.0.1')), new PhpSerializer());
            $this->queue->flush();
        } catch (ConnectionException $e) {
            static::markTestSkipped("cannot connect to beanstalkd");
        }
        $this->container = new Container();
        $this->worker = new Worker($this->queue, $this->container);
    }

    public function testNormalPing()
    {
        $event = new EventEmitter([
            NormalPing::class => [
                NormalPong::class,
                QueuePong::class,
            ],
        ]);
        $event->setContainer($this->container);
        $event->setWorker($this->worker);

        static::assertOutputBufferEquals("[NORMAL PONG] normal ping message\n", function () use ($event) {
            $event->trigger(new NormalPing("normal ping message"));
        });
        
        $this->worker->work(WorkerStopper::class, 'stop'); // will stop automatically
        static::assertOutputBufferEquals("[QUEUE PONG] normal ping message\n", function () {
            $this->worker->listen();
        });
    }

    public function testQueuePing()
    {
        $event = new EventEmitter([
            QueuePing::class => [
                NormalPong::class,
                QueuePong::class,
            ],
        ]);
        $event->setContainer($this->container);
        $event->setWorker($this->worker);

        static::assertOutputBufferEquals("", function () use ($event) {
            $event->trigger(new QueuePing("queue ping message"));
        });

        $this->worker->work(WorkerStopper::class, 'stop'); // will stop automatically
        static::assertOutputBufferEquals("[NORMAL PONG] queue ping message\n[QUEUE PONG] queue ping message\n", function () {
            $this->worker->listen();
        });
    }
}
