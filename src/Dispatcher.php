<?php
namespace Wandu\Router;

use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
use FastRoute\Dispatcher as FastDispatcher;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;

class Dispatcher
{
    /** @var \Wandu\Router\Contracts\ClassLoaderInterface */
    protected $classLoader;

    /**
     * @param \Wandu\Router\Contracts\ClassLoaderInterface $loader
     */
    public function __construct(ClassLoaderInterface $loader)
    {
        $this->classLoader = $loader;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Router\Router $router
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request, Router $router)
    {
        $dispatcher = new GCBDispatcher($router->getCollector()->getData());
        $method = $request->getMethod();

        // virtual method
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['_method'])) {
            $method = strtoupper($parsedBody['_method']);
        }
        $routeInfo = $this->runDispatcher($dispatcher, $method, $request->getUri()->getPath());
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        return $router->getRoutes()[$routeInfo[1]]->execute($request, $this->classLoader);
    }

    /**
     * @param \FastRoute\Dispatcher $dispatcher
     * @param string $method
     * @param string $path
     * @return array
     */
    protected function runDispatcher(FastDispatcher $dispatcher, $method, $path)
    {
        $routeInfo = $dispatcher->dispatch($method, $path);
        switch ($routeInfo[0]) {
            case FastDispatcher::NOT_FOUND:
                throw new RouteNotFoundException();
            case FastDispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();
            case FastDispatcher::FOUND:
                return $routeInfo;
        }
    }
}
