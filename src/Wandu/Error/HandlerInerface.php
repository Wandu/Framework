<?php
namespace Wandu\Error;

use Throwable;

interface HandlerInterface
{
    const RETURN_EXIT = 1;
    const RETURN_CONTINUE = 2;

    /**
     * @param \Throwable $exception
     * @return int
     */
    public function handle(Throwable $exception);
}
