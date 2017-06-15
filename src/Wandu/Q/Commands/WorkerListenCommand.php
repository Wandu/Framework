<?php
namespace Wandu\Q\Commands;

use Wandu\Console\Command;
use Wandu\Q\Worker;

class WorkerListenCommand extends Command
{
    /** @var \Wandu\Q\Worker */
    protected $worker;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    function execute()
    {
        $this->worker->listen();
    }
}
