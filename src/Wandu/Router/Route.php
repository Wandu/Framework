<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Contracts\Route as RouteContract;

class Route implements RouteContract
{
    /**
     * @param array $dataSet
     * @return static
     */
    public static function __set_state(array $dataSet)
    {
        return new static(
            $dataSet['className'],
            $dataSet['methodName'],
            $dataSet['middlewares'],
            $dataSet['host'] ?? null,
            $dataSet['name'] ?? null
        );
    }

    /** @var string */
    protected $className;

    /** @var string */
    protected $methodName;

    /** @var array */
    protected $middlewares;

    /** @var string */
    protected $host;

    /** @var string */
    protected $name;

    /**
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @param string $host
     * @param string $name
     */
    public function __construct($className, $methodName, array $middlewares = [], $host = null, $name = null)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->middlewares = $middlewares;
        $this->host = $host;
        $this->name = $name;
    }

    /**
     * @param string|array $middlewares
     * @param bool $overwrite
     * @return \Wandu\Router\Contracts\Route|self
     */
    public function middleware($middlewares, $overwrite = false): RouteContract
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }
        $this->middlewares = $overwrite
            ? $middlewares
            : array_merge($this->middlewares, $middlewares);
        return $this;
    }

    /**
     * @param string $name
     * @return \Wandu\Router\Contracts\Route|self
     */
    public function name(string $name): RouteContract
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @internal
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $host
     * @return \Wandu\Router\Route|self
     */
    public function host(string $host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Router\Contracts\LoaderInterface|null $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface|null $responsifier
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute(
        ServerRequestInterface $request,
        LoaderInterface $loader = null,
        ResponsifierInterface $responsifier = null
    ) {
        $pipeline = new RouteExecutor($loader, $responsifier);
        return $pipeline->execute($request, $this->className, $this->methodName, $this->middlewares);
    }
}
