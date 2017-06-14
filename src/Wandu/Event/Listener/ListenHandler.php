<?php
namespace Wandu\Event\Listener;

use Wandu\Event\Contracts\Listener;

class ListenHandler implements Listener
{
    /**
     * {@inheritdoc}
     */
    public function call(array $arguments = [])
    {
        if (method_exists($this, 'handle')) {
            $this->handle(...$arguments);
        }
    }
}
