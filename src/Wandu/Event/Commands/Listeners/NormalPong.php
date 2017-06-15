<?php
namespace Wandu\Event\Commands\Listeners;

use Wandu\Event\Commands\Events\NormalPing;
use Wandu\Event\Listener\ListenHandler;

class NormalPong extends ListenHandler
{
    public function handle(NormalPing $ping)
    {
        echo "[NORMAL PONG] {$ping->getMessage()}\n";
    }
}
