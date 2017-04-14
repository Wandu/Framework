<?php
namespace Wandu\Event\Listeners;

use Wandu\Event\Events\Ping;
use Wandu\Event\Listener;

class Pong extends Listener
{
    /**
     * @param \Wandu\Event\Events\Ping $ping
     */
    public function handle(Ping $ping)
    {
        echo "[PONG] {$ping->getMessage()}\n";
    }
}
