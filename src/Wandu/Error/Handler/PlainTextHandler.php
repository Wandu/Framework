<?php
namespace Wandu\Error\Handler;

use Psr\Log\LoggerInterface;
use Throwable;
use Wandu\Error\HandlerInterface;

class PlainTextHandler implements HandlerInterface
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $exception)
    {
        echo sprintf("%s: %s in file %s on line %d\n",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        return HandlerInterface::RETURN_EXIT;
    }
}
