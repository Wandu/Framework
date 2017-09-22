<?php
namespace Wandu\Router;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\Dispatchable;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Loader\SimpleLoader;
use Wandu\Router\Responsifier\NullResponsifier;

class Dispatcher
{
    /** @var \Wandu\Router\Contracts\LoaderInterface */
    protected $loader;

    /** @var \Wandu\Router\Responsifier\NullResponsifier */
    protected $responsifier;

    /** @var array */
    protected $options;
    
    public function __construct(
        LoaderInterface $loader = null,
        ResponsifierInterface $responsifier = null,
        array $options = []
    ) {
        $this->loader = $loader ?: new SimpleLoader();
        $this->responsifier = $responsifier ?: new NullResponsifier();
        $this->setOptions($options);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge([
            'method_override_enabled' => true,
            'method_spoofing_enabled' => false,
            'defined_prefix' => '',
            'defined_middlewares' => [],
            'defined_domains' => [],
        ], $options);
    }

    /**
     * @return \Wandu\Router\RouteCollection
     */
    public function createRouteCollection(): RouteCollection
    {
        return new RouteCollection(
            $this->options['defined_prefix'],
            (array)($this->options['defined_middlewares']),
            (array)($this->options['defined_domains'])
        );
    }
    
    /**
     * @param \Wandu\Router\Contracts\Dispatchable $dispatcher
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(Dispatchable $dispatcher, ServerRequestInterface $request)
    {
        return $dispatcher->dispatch($this->loader, $this->responsifier, $this->applyVirtualMethod($request));
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function applyVirtualMethod(ServerRequestInterface $request)
    {
        if ($this->options['method_override_enabled'] && $request->hasHeader('X-Http-Method-Override')) {
            return $request->withMethod(strtoupper($request->getHeaderLine('X-Http-Method-Override')));
        }
        if ($this->options['method_spoofing_enabled']) {
            $parsedBody = $request->getParsedBody();
            if (isset($parsedBody['_method'])) {
                return $request->withMethod($parsedBody['_method']);
            }
        }
        return $request;
    }
}
