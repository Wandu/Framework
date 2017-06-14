<?php
namespace Wandu\Event\Listener;

use Wandu\Event\Contracts\Listener;

class CallableListener implements Listener
{
    /** @var callable */
    protected $handler;
    
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function call(array $arguments = [])
    {
        call_user_func_array($this->handler, $arguments);
    }
}
