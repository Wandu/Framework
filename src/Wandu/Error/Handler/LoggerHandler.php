<?php
namespace Wandu\Error\Handler;

use Psr\Log\LoggerInterface;
use Throwable;
use Wandu\Error\HandlerInterface;

class LoggerHandler implements HandlerInterface
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $exception)
    {
        $this->logger->error($exception);
        return HandlerInterface::RETURN_CONTINUE;
    }
}
