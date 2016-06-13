<?php
namespace Wandu\Console\Commands;

use Psy\Shell;
use Wandu\Console\Command;
use Wandu\Q\Queue;

class PsyshCommand extends Command
{
    /** @var string */
    protected $description = "Execute psysh with Wandu";

    function execute()
    {
        $sh = new Shell();
        $sh->run();
    }
}
