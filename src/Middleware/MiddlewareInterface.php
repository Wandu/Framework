<?php
namespace Wandu\Router\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, callable $next);
}
