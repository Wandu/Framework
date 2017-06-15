<?php
namespace Wandu\Q;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Wandu\Q\Exception\WorkerStopException;

class Worker
{
    /** @var \Wandu\Q\Queue */
    protected $queue;
    
    /** @var \Psr\Container\ContainerInterface */
    protected $container;
    
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    
    /** @var bool */
    protected $running = false;
    
    public function __construct(Queue $queue, ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->queue = $queue;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * @return void 
     */
    public function stop()
    {
        if ($this->logger) {
            $this->logger->info("stop by stop method.");
        }
        $this->running = false;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $arguments
     */
    public function work($class, $method, $arguments = [])
    {
        $this->queue->send([
            'class' => $class,
            'method' => $method,
            'arguments' => $arguments,
        ]);
    }
    
    /**
     * @param int $tick
     */
    public function listen($tick = 200000)
    {
        $this->running = true;
        $signalEnabled = false;
        if (function_exists('pcntl_signal') && function_exists('pcntl_signal_dispatch')) {
            pcntl_signal(SIGINT, [$this, 'stop']);
            pcntl_signal(SIGTERM, [$this, 'stop']);
            pcntl_signal(SIGHUP, [$this, 'stop']);
            $signalEnabled = true;
        } elseif ($this->logger) {
            $this->logger->info('if use pcntl_*, work more safety.');
        }
        try {
            while ($this->running) {
                if ($job = $this->queue->receive()) {
                    $result = $job->read();
                    call_user_func_array([
                        $this->container->get($result['class']),
                        $result['method']
                    ], $result['arguments'] ?? []);
                    if ($this->logger) {
                        $this->logger->info(sprintf("execute %s@%s", $result['method'], $result['class']));
                    }
                    $job->delete();
                }
                if ($signalEnabled) {
                    pcntl_signal_dispatch();
                }
                usleep($tick);
            }
        } catch (WorkerStopException $e) {
            if ($this->logger) {
                $this->logger->info("stop by WorkerStopException.");
            }
        }
    }
}
