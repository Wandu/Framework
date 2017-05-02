<?php
namespace Wandu\Router;

use Closure;
use FastRoute\DataGenerator\GroupCountBased as GCBGenerator;
use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Path\Pattern;

class Dispatcher
{
    /** @var \Wandu\Router\Contracts\LoaderInterface */
    protected $loader;

    /** @var \Wandu\Router\Responsifier\NullResponsifier */
    protected $responsifier;
    
    /** @var \Wandu\Router\Configuration */
    protected $config;
    
    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var \Wandu\Router\Route[] */
    protected $namedPattern;
    
    /** @var array */
    protected $compiledRoutes = [];

    public function __construct(
        LoaderInterface $loader = null,
        ResponsifierInterface $responsifier = null,
        Configuration $config = null
    ) {
        $this->loader = $loader;
        $this->responsifier = $responsifier;
        $this->config = $config ?: new Configuration([]);
    }

    public function flush()
    {
        if ($this->config->isCacheEnabled()) {
            @unlink($this->config->getCacheFile());
        }
    }

    /**
     * @deprecated use setRoutes
     * @param \Closure $routes
     * @return \Wandu\Router\Dispatcher
     */
    public function withRoutes(Closure $routes)
    {
        $inst = clone $this;
        $inst->setRoutes($routes);
        return $inst;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function getPath($name, array $attributes = [])
    {
        if (!isset($this->namedPattern[$name])) {
            throw new RouteNotFoundException("Route \"{$name}\" not found.");
        }
        $pattern = new Pattern($this->namedPattern[$name]);
        return $pattern->path($attributes);
    }

    /**
     * @param \Closure $routes
     */
    public function setRoutes(Closure $routes)
    {
        $cacheEnabled = $this->config->isCacheEnabled();
        if ($this->isCached()) {
            $cache = $this->restoreCache();
            $this->compiledRoutes = $cache['dispatch_data'];
            $this->routes = $cache['routes'];
            $this->namedPattern = isset($cache['named_routes']) ? $cache['named_routes'] : [];
        } else {
            $resultRoutes = [];
            $resultNamedPath = [];
            
            $router = new Router;
            $router->middleware($this->config->getMiddleware(), $routes);
            
            $generator = new GCBGenerator();
            /**
             * @var array|string[] $methods
             * @var string $path
             * @var \Wandu\Router\Route $route
             */
            foreach ($router as list($methods, $path, $route)) {
                $pathPattern = new Pattern($path);
                if ($routeName = $route->getName()) {
                    $resultNamedPath[$routeName] = $path;
                }
                foreach ($pathPattern->parse() as $parsedPath) {
                    foreach ($methods as $method) {
                        $handleId = uniqid('HANDLER');
                        $generator->addRoute($method, $parsedPath, $handleId);
                        $resultRoutes[$handleId] = $route;
                    }
                }
            }
            $this->routes = $resultRoutes;
            $this->namedPattern = $resultNamedPath;
            $this->compiledRoutes = $generator->getData();
            
            if ($cacheEnabled) {
                $this->storeCache([
                    'dispatch_data' => $this->compiledRoutes,
                    'routes' => $this->routes,
                    'named_routes' => $this->namedPattern,
                ]);
            }
        }
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $request = $this->applyVirtualMethod($request);
        $routeInfo = $this->runDispatcher(
            new GCBDispatcher($this->compiledRoutes),
            $request->getMethod(),
            $request->getUri()->getPath()
        );
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        return $this->routes[$routeInfo[1]]->execute($request, $this->loader, $this->responsifier);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function applyVirtualMethod(ServerRequestInterface $request)
    {
        if (!$this->config->isVirtualMethodEnabled()) {
            return $request;
        }
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['_method'])) {
            return $request->withMethod(strtoupper($parsedBody['_method']));
        }
        if ($request->hasHeader('X-Http-Method-Override')) {
            return $request->withMethod(strtoupper($request->getHeaderLine('X-Http-Method-Override')));
        }
        return $request;
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

    /**
     * @return bool
     */
    public function isCached(): bool
    {
        $cacheEnabled = $this->config->isCacheEnabled();
        $cacheFile = $this->config->getCacheFile();
        return $cacheEnabled && file_exists($cacheFile);
    }
    
    /**
     * @param array $attributes
     */
    private function storeCache(array $attributes = [])
    {
        file_put_contents($this->config->getCacheFile(), '<?php return ' . var_export($attributes, true) .';');
    }

    /**
     * @return array
     */
    private function restoreCache()
    {
        return require $this->config->getCacheFile();
    }
}
