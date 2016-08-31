<?php
namespace Wandu\Router\ClassLoader;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\Containee\BindContainee;
use Wandu\DI\ContainerInterface;
use Wandu\Http\Attribute\LazyAttribute;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Cookie\CookieJar;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\QueryParams;
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

        $request = $request->withAttribute('parsed_body', new LazyAttribute(function () use ($container) {
            return $container->get(ParsedBodyInterface::class);
        }))->withAttribute('query_params', new LazyAttribute(function ()  use ($container) {
            return $container->get(QueryParamsInterface::class);
        }));

        $container->instance(ServerRequest::class, $request);
        $container->alias(ServerRequestInterface::class, ServerRequest::class);
        $container->alias('request', ServerRequest::class);

        $container->bind(QueryParamsInterface::class, QueryParams::class);
        $container->bind(ParsedBodyInterface::class, ParsedBody::class);

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
        return $container->call([$object, $methodName]);
    }
}
