<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\MiddlewareInterface;

class CookieMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, callable $next)
    {
        $request = $request->withAttribute('cookie', ['name' => 'wan2land']);
        return $next($request);
    }
}
