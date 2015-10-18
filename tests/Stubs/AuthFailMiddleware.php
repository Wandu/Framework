<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Middleware\MiddlewareInterface;

class AuthFailMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, callable $next)
    {
        return "auth fail...";
    }
}
