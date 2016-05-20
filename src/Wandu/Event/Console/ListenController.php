<?php
namespace Wandu\Event\Console;

use Wandu\Console\Controller;
use Wandu\Event\Dispatcher;
use Wandu\Q\Queue;

class ListenController extends Controller
{
    const EXECUTE_TIMEOUT = 2;

    /** @var string */
    protected $description = "Listen queued events";
    
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
        $this->output->writeln("Start Queued Events Listener..");
        while (1) {
            $job = $this->queue->dequeue();
            if (isset($job)) {
                $payload = $job->read();
                if (is_array($payload) && array_key_exists('method', $payload) && $payload['method'] === 'event:execute') {
                    $this->output->writeln(date('[ymd_His]').' Received!');
                    $this->dispatcher->executeListeners($payload['event']);
                    $job->delete();
                }
            } else {
                sleep(static::EXECUTE_TIMEOUT);
            }
        }
    }
}
