<?php
namespace Jicjjang\June\Stubs;

use Closure;
use Jicjjang\June\ControllerInterface;
use Psr\Http\Message\RequestInterface;

class AdminController implements ControllerInterface
{
    /**
     * @param RequestInterface $request
     * @param callable $next
     * @return mixed
     */
    public function middleware(RequestInterface $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    public function action(RequestInterface $request)
    {
        return "Hello World!!!";
    }
}
