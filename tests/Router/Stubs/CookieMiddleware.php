<?php
namespace Wandu\Router\Stubs;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\MiddlewareInterface;

class CookieMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $request = $request->withAttribute('cookie', ['name' => 'wan2land']);
        return $next($request);
    }
}
