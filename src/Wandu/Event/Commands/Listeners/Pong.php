<?php
namespace Wandu\Event\Listeners;

use Wandu\Event\Events\Ping;
use Wandu\Event\Listener\ListenHandler;

class Pong extends ListenHandler
{
    /**
     * @param \Wandu\Event\Events\Ping $ping
     */
    public function handle(Ping $ping)
    {
        echo "[PONG] {$ping->getMessage()}\n";
    }
}
