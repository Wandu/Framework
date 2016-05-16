<?php
namespace Wandu\Console\Controllers;

use Wandu\Console\Controller;
use Wandu\Console\Reader;
use Wandu\Event\Dispatcher;
use Wandu\Event\EventInterface;
use Wandu\Q\Queue;

class HelloWorld extends Controller
{
    public function __construct(Reader $reader, Dispatcher $event)
    {
        $this->reader = $reader;
        $this->event = $event;
    }

    public function execute()
    {
        $this->event->trigger(new class implements EventInterface {
            public function getEventName()
            {
                return "HelloWorld";
            }
        });
//        $this->output->writeln("Hello World :-)");
//        $this->output->write("Insert Text : ");
//        $context = $this->reader->read();
//        $this->output->writeln($context);
    }
}
