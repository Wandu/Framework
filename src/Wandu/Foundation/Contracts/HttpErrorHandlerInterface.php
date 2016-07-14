<?php
namespace Wandu\Foundation\Contracts;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;

interface HttpErrorHandlerInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Throwable|\Exception $exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request, $exception);
}
