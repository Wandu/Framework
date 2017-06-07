<?php
namespace Wandu\Foundation\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface HttpErrorHandler
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Throwable|\Exception $exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request, $exception);
}
