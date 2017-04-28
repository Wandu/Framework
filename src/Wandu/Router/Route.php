<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;

class Route
{
    /**
     * @param array $dataSet
     * @return static
     */
    public static function __set_state(array $dataSet)
    {
        return new static(
            $dataSet['name'],
            $dataSet['className'],
            $dataSet['methodName'],
            $dataSet['middlewares']
        );
    }

    /** @var string */
    protected $name;
    
    /** @var string */
    protected $className;

    /** @var string */
    protected $methodName;

    /** @var array */
    protected $middlewares;

    /**
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @param string $name
     */
    public function __construct($className, $methodName, array $middlewares = [], $name = null)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->middlewares = $middlewares;
        $this->name = $name;
    }

    /**
     * @param string|array $middlewares
     * @param bool $overwrite
     * @return \Wandu\Router\Route|self
     */
    public function middleware($middlewares, $overwrite = false)
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
     * @return \Wandu\Router\Route|self
     */
    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
