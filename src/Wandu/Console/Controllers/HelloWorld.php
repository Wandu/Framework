<?php
namespace Wandu\Console\Controllers;

use Wandu\Console\Controller;

class HelloWorld extends Controller
{
    public function execute()
    {
        $this->output->writeln("Hello World :-)");
    }
}
