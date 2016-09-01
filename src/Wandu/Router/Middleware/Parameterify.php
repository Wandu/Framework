<?php
namespace Wandu\Router\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Attribute\LazyAttribute;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
use Wandu\Router\Contracts\MiddlewareInterface;

class Parameterify implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $request = $request
            ->withAttribute('parsed_body', new LazyAttribute(function (ServerRequestInterface $request) {
                return new ParsedBody($request);
            }))
            ->withAttribute('query_params', new LazyAttribute(function (ServerRequestInterface $request) {
                return new QueryParams($request);
            }));
        
        return $next($request);
    }
}
