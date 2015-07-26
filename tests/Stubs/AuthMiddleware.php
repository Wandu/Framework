<?php
namespace Wandu\Router\Stubs;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        return $next($request) . ' middleware~';
    }
}
