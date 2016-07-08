<?php
namespace Wandu\Router\Stubs;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\MiddlewareInterface;

class AuthFailMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        return "[{$request->getMethod()}] auth fail;";
    }
}
