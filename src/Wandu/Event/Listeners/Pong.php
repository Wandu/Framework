<?php
namespace Wandu\Event\Listeners;

use Wandu\Event\Events\Ping;
use Wandu\Event\Listener;
use Wandu\Q\Queue;

class Pong extends Listener
{
    public function handle(Ping $ping)
    {
        echo "[PONG] {$ping->getMessage()}";
    }
}
