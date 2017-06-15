<?php
namespace Wandu\Event\Commands\Events;

use Wandu\Event\Contracts\ViaQueue;

class QueuePing extends NormalPing implements ViaQueue
{
}
