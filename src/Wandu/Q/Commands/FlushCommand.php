<?php
namespace Wandu\Q\Commands;

use Psr\Log\LoggerInterface;
use Wandu\Console\Command;
use Wandu\Q\Queue;

class FlushCommand extends Command
{
    /** @var \Wandu\Q\Queue */
    protected $queue;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(Queue $queue, LoggerInterface $logger)
    {
        $this->queue = $queue;
        $this->logger = $logger;
    }

    function execute()
    {
        try {
            while (1) {
                $job = $this->queue->dequeue();
                if (!isset($job)) {
                    return;
                }
                $payload = $job->read();
                $this->output->writeln("  Flush, " . print_r($payload, true));
                $job->delete();
            }
        } catch (\Exception $e) {
            $this->logger->alert($e);
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->alert($e);
            throw $e;
        }
    }
}
