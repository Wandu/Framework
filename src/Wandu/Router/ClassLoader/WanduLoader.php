<?php
namespace Wandu\Router\ClassLoader;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\ContainerInterface;
use Wandu\Http\Attribute\LazyAttribute;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\ServerParamsInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Cookie\CookieJar;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
use Wandu\Http\Parameters\ServerParams;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Session\Session;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class WanduLoader implements ClassLoaderInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;

    /**
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function create($className)
    {
        if (!class_exists($className)) {
            throw new HandlerNotFoundException($className);
        }
        return $this->container->create($className);
    }

    /**
     * {@inheritdoc}
     */
    public function call(ServerRequestInterface $request, $object, $methodName)
    {
        if (!method_exists($object, $methodName) && !method_exists($object, '__call')) {
            throw new HandlerNotFoundException(get_class($object), $methodName);
        }
        // instance container
        $container = $this->container->with(); // clone

        $this->bindParameter($container, $request);
        $this->bindServerRequest($container, $request);

        return $container->call([$object, $methodName]);
    }

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    private function bindParameter(ContainerInterface $container, ServerRequestInterface $request)
    {
        if ($queryParams = $request->getAttribute('server_params')) {
            $container->instance(ServerParams::class, $queryParams);
            $container->alias(ServerParamsInterface::class, ServerParams::class);
            $container->alias('server_params', ServerParams::class);
        }
        if ($queryParams = $request->getAttribute('query_params')) {
            $container->instance(QueryParams::class, $queryParams);
            $container->alias(QueryParamsInterface::class, QueryParams::class);
            $container->alias('query_params', QueryParams::class);
        }
        if ($parsedBody = $request->getAttribute('parsed_body')) {
            $container->instance(ParsedBody::class, $parsedBody);
            $container->alias(ParsedBodyInterface::class, ParsedBody::class);
            $container->alias('parsed_body', ParsedBody::class);
        }
        if ($cookie = $request->getAttribute('cookie')) {
            $container->instance(CookieJar::class, $cookie);
            $container->alias(CookieJarInterface::class, CookieJar::class);
            $container->alias('cookie', CookieJar::class);
        }
        if ($session = $request->getAttribute('session')) {
            $container->instance(Session::class, $session);
            $container->alias(SessionInterface::class, Session::class);
            $container->alias('session', Session::class);
        }
    }

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    private function bindServerRequest(ContainerInterface $container, ServerRequestInterface $request)
    {
        $container->instance(ServerRequest::class, $request);
        $container->alias(ServerRequestInterface::class, ServerRequest::class);
        $container->alias('request', ServerRequest::class);
    }
}
