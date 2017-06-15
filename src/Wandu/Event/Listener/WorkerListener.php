<?php
namespace Wandu\Event\Listener;

use Wandu\Event\Contracts\Listener;
use Wandu\Q\Worker;

class WorkerListener implements Listener
{
    /** @var \Wandu\Q\Worker */
    protected $worker;
    
    /** @var string */
    protected $className;
    
    public function __construct(Worker $worker, string $className)
    {
        $this->worker = $worker;
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function call(array $arguments = [])
    {
        $this->worker->work($this->className, 'call', [$arguments]);
    }
}
