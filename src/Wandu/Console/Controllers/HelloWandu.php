<?php
namespace Wandu\Console\Controllers;

use Wandu\Console\Controller;
use Wandu\Q\Queue;

class HelloWandu extends Controller
{
    /** @var string */
    protected $description = "\"Hello Wandu!\"";
    
    public function execute()
    {
        $this->output->writeln("Hello Wandu!");
    }
}
