<?php
namespace Wandu\Router;

use Closure;
use FastRoute\Dispatcher as FastDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;

class Dispatcher
{
    /** @var \Wandu\Router\Contracts\LoaderInterface */
    protected $loader;

    /** @var \Wandu\Router\Responsifier\NullResponsifier */
    protected $responsifier;
    
    /** @var \Wandu\Router\Configuration */
    protected $config;
    
    /** @var \Closure */
    protected $handler;
    
    /** @var \Wandu\Router\CompiledRoutes */
    protected $compiledRoutes;
    
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
     * 
     * @param \Closure $handler
     * @return \Wandu\Router\Dispatcher
     */
    public function withRoutes(Closure $handler)
    {
        $inst = clone $this;
        $inst->setRoutes($handler);
        return $inst;
    }

    /**
     * @param \Closure $handler
     */
    public function setRoutes(Closure $handler)
    {
        $this->compiledRoutes = null;
        $this->handler = $handler;
    }
    
    /**
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function getPath($name, array $attributes = [])
    {
        return $this->getCompiledRoutes()->getPattern($name)->path($attributes);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $compiledRoutes = $this->getCompiledRoutes();
        $request = $this->applyVirtualMethod($request);
        return $compiledRoutes->dispatch($request, $this->loader, $this->responsifier);
    }
    
    protected function getCompiledRoutes(): CompiledRoutes
    {
        if (!$this->compiledRoutes) {
            $cacheEnabled = $this->config->isCacheEnabled();
            if ($this->isCached()) {
                $this->compiledRoutes = $this->restoreCache();
            } else {
                $this->compiledRoutes = CompiledRoutes::compile($this->handler, $this->config);
                if ($cacheEnabled) {
                    $this->storeCache($this->compiledRoutes);
                }
            }
        }
        return $this->compiledRoutes;
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
     * @return bool
     */
    public function isCached(): bool
    {
        $cacheEnabled = $this->config->isCacheEnabled();
        $cacheFile = $this->config->getCacheFile();
        return $cacheEnabled && file_exists($cacheFile);
    }
    
    private function storeCache(CompiledRoutes $routes)
    {
        file_put_contents($this->config->getCacheFile(), serialize($routes));
    }

    private function restoreCache(): CompiledRoutes
    {
        return unserialize(file_get_contents($this->config->getCacheFile()));
    }
}
