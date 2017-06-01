<?php
namespace Wandu\Event\Commands;

use Wandu\Console\Command;
use Wandu\Event\Events\Ping;
use function Wandu\Event\trigger;

class PingCommand extends Command
{
    /** @var string */
    protected $description = "Queue a \"Ping\" event for testing";

    function execute()
    {
        $this->output->writeln("Send Ping Event..");
        trigger(new Ping("Ping..."));
    }
}
