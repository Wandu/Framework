<?php
namespace Wandu\Event\Contracts;

interface EventEmitter
{
    /**
     * @param string $event
     * @param string|\Closure $listener
     * @return void
     */
    public function on(string $event, $listener);

    /**
     * @param string $event
     * @param string|\Closure|\Wandu\Event\Contracts\Listener $listener
     * @return void
     */
    public function off(string $event, $listener = null);

    /**
     * @param string|object $event
     * @param array ...$arguments
     * @return void
     */
    public function trigger($event, ...$arguments);
}
