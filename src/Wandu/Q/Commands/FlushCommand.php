<?php
namespace Wandu\Q\Commands;

use Wandu\Console\Command;
use Wandu\Q\Queue;

class FlushCommand extends Command
{
    /** @var \Wandu\Q\Queue */
    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    function execute()
    {
        $this->queue->flush();
    }
}
