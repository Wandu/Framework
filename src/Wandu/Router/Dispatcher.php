<?php
namespace Wandu\Router;

use Closure;
use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;

class Dispatcher
{
    /** @var \Wandu\Router\Contracts\ClassLoaderInterface */
    protected $classLoader;

    /** @var \Closure */
    protected $routing;

    /**
     * @param \Wandu\Router\Contracts\ClassLoaderInterface $loader
     * @param array $config
     */
    public function __construct(ClassLoaderInterface $loader, array $config = [])
    {
        $this->classLoader = $loader;
        $this->config = $config + [
            'virtual_method_enabled' => false,
            'cache_enabled' => false,
            'cache_file' => null,
        ];
    }

    public function flush()
    {
        if ($this->config['cache_enabled']) {
            @unlink($this->config['cache_file']);
        }
    }

    /**
     * @param \Closure $routing
     * @return \Wandu\Router\Dispatcher
     */
    public function withRouter(Closure $routing)
    {
        $inst = clone $this;
        $inst->routing = $routing;
        return $inst;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $cacheEnabled = $this->config['cache_enabled'];

        $cacheFile = $this->config['cache_file'];
        if ($cacheEnabled && file_exists($cacheFile)) {
            $cacheData = require $cacheFile;
            $dispatchData = $cacheData['dispatch_data'];
            $routes = $cacheData['routes'];
        } else {
            $router = new Router;
            $this->routing->__invoke($router);
            $dispatchData = $router->getCollector()->getData();
            $routes = $router->getRoutes();
        }

        if ($cacheEnabled) {
            file_put_contents($cacheFile, '<?php return ' . var_export([
                    'dispatch_data' => $dispatchData,
                    'routes' => $routes,
                ], true) .';');
        }

        $request = $this->applyVirtualMethod($request);
        $routeInfo = $this->runDispatcher(
            new GCBDispatcher($dispatchData),
            $request->getMethod(),
            $request->getUri()->getPath()
        );
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        return $routes[$routeInfo[1]]->execute($request, $this->classLoader);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function applyVirtualMethod(ServerRequestInterface $request)
    {
        if (!$this->config['virtual_method_enabled']) {
            return $request;
        }
        $parsedBody = $request->getParsedBody();
        if (!isset($parsedBody['_method'])) {
            return $request;
        }
        return $request->withMethod(strtoupper($parsedBody['_method']));
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
