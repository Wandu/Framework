<?php
namespace Wandu\Event\Console;

use Wandu\Console\Controller;
use Wandu\Event\Dispatcher;
use Wandu\Event\Events\Ping;
use Wandu\Q\Queue;

class TriggerPingController extends Controller
{
    public function __construct(Queue $queue, Dispatcher $dispatcher)
    {
        $this->queue = $queue;
        $this->dispatcher = $dispatcher;
    }

    function execute()
    {
        $this->output->writeln("Send Test Event..");
        $this->dispatcher->trigger(new Ping("ping :-)"));
    }
}
