<?php
namespace Wandu\Console\Controllers;

use Wandu\Console\Controller;
use Wandu\Console\Reader;

class HelloWorld extends Controller
{
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function execute()
    {
        $this->output->writeln("Hello World :-)");
        $this->output->write("Insert Text : ");
        $context = $this->reader->read();
        $this->output->writeln($context);
    }
}
