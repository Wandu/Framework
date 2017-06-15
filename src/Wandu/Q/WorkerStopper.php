<?php
namespace Wandu\Q;

use Wandu\Q\Exception\WorkerStopException;

class WorkerStopper
{
    public function stop()
    {
        throw new WorkerStopException();
    }
}
