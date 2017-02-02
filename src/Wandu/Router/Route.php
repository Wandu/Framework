<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
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
            $dataSet['className'],
            $dataSet['methodName'],
            $dataSet['middlewares']
        );
    }

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
     */
    public function __construct($className, $methodName, array $middlewares = [])
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->middlewares = $middlewares;
    }

    /**
     * @param string|array $middlewares
     * @param bool $overwrite
     * @return \Wandu\Router\Route
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
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Router\Contracts\ClassLoaderInterface|null $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface|null $responsifier
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute(
        ServerRequestInterface $request,
        ClassLoaderInterface $loader = null,
        ResponsifierInterface $responsifier = null
    ) {
        $pipeline = new RouteExecutor($loader, $responsifier);
        return $pipeline->execute($request, $this->className, $this->methodName, $this->middlewares);
    }
}
