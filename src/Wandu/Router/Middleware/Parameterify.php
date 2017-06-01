<?php
namespace Wandu\Router\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\ServerParamsInterface;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
use Wandu\Http\Parameters\ServerParams;
use Wandu\Router\Contracts\MiddlewareInterface;

class Parameterify implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $serverParams = new ServerParams($request);
        $queryParams = new QueryParams($request);
        $parsedBody = new ParsedBody($request);
        
        $request = $request
            ->withAttribute('server_params', $serverParams)
            ->withAttribute('serverParams', $serverParams)
            ->withAttribute(ServerParams::class, $serverParams)
            ->withAttribute(ServerParamsInterface::class, $serverParams)
            ->withAttribute('query_params', $queryParams)
            ->withAttribute('queryParams', $queryParams)
            ->withAttribute(QueryParams::class, $queryParams)
            ->withAttribute(QueryParamsInterface::class, $queryParams)
            ->withAttribute('parsed_body', $parsedBody)
            ->withAttribute('parsedBody', $parsedBody)
            ->withAttribute(ParsedBody::class, $parsedBody)
            ->withAttribute(ParsedBodyInterface::class, $parsedBody);

        return $next($request);
    }
}
