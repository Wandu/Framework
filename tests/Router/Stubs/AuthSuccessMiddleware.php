<?php
namespace Wandu\Router\Stubs;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Router\Contracts\MiddlewareInterface;

class AuthSuccessMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $next($request);
        $message = "[{$request->getMethod()}] auth success; " . $response->getBody()->__toString();
        
        return $response->withBody(new StringStream($message));
    }
}
