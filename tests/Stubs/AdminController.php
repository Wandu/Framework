<?php
namespace Jicjjang\June\Stubs;

use Closure;
use Jicjjang\June\ControllerInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminController implements ControllerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function middleware(ServerRequestInterface $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function action(ServerRequestInterface $request)
    {
        return "Hello World!!!";
    }
}
