<?php
namespace Wandu\Event\Commands\Listeners;

use Wandu\Event\Contracts\ViaQueue;
use Wandu\Event\Commands\Events\NormalPing;
use Wandu\Event\Listener\ListenHandler;

class QueuePong extends ListenHandler implements ViaQueue
{
    /**
     * @param \Wandu\Event\Commands\Events\NormalPing $ping
     */
    public function handle(NormalPing $ping)
    {
        echo "[QUEUE PONG] {$ping->getMessage()}\n";
    }
}
