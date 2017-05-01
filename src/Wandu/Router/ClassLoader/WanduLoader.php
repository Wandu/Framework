<?php
namespace Wandu\Router\ClassLoader;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Wandu\DI\ContainerInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\ServerParamsInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Parameters\CookieJar;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
use Wandu\Http\Parameters\ServerParams;
use Wandu\Http\Parameters\Session;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class WanduLoader implements LoaderInterface
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
    public function middleware($className): MiddlewareInterface
    {
        try {
            return $this->container->create($className);
        } catch (Exception $e) {
            throw new HandlerNotFoundException($className);
        } catch (Throwable $e) {
            throw new HandlerNotFoundException($className);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute($className, $methodName, ServerRequestInterface $request)
    {
        try {
            $object = $this->container->get($className);
        } catch (Exception $e) {
            throw new HandlerNotFoundException($className, $methodName);
        } catch (Throwable $e) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        if (!method_exists($object, $methodName) && !method_exists($object, '__call')) {
            throw new HandlerNotFoundException($className, $methodName);
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
        if ($serverParams = $request->getAttribute('server_params')) {
            $container->instance(ServerParams::class, $serverParams);
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
