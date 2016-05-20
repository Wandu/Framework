<?php
namespace Wandu\Event\Console;

use Wandu\Console\Controller;
use Wandu\Event\Dispatcher;
use Wandu\Event\Events\Ping;
use Wandu\Q\Queue;

class PingController extends Controller
{
    /** @var string */
    protected $description = "Queue a \"Ping\" event for testing";

    /**
     * @param \Wandu\Q\Queue $queue
     * @param \Wandu\Event\Dispatcher $dispatcher
     */
    public function __construct(Queue $queue, Dispatcher $dispatcher)
    {
        $this->queue = $queue;
        $this->dispatcher = $dispatcher;
    }

    function execute()
    {
        $this->output->writeln("Send Ping Event..");
        $this->dispatcher->trigger(new Ping("Ping..."));
    }
}
