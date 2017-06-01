<?php
namespace Wandu\Router\Loader;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use RuntimeException;
use ReflectionException;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class PsrLoader implements LoaderInterface
{
    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function middleware(string $className): MiddlewareInterface
    {
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }
        throw new HandlerNotFoundException($className);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $className, string $methodName, ServerRequestInterface $request)
    {
        if ($this->container->has($className)) {
            $object = $this->container->get($className);
            try {
                $reflMethod = (new ReflectionClass($className))->getMethod($methodName);
            } catch (ReflectionException $e) {
                $reflMethod = null;
            }
            if ($reflMethod) {
                $arguments = $this->getArguments($reflMethod, $request);
                return call_user_func_array([$object, $methodName], $arguments);
            }
            if (method_exists($object, '__call')) {
                return $object->__call($methodName, [$request]);
            }
        }
        throw new HandlerNotFoundException($className, $methodName);
    }
    
    public function getArguments(\ReflectionFunctionAbstract $refl, ServerRequestInterface $request)
    {
        $arguments = [];
        // it from container Resolver..
        foreach ($refl->getParameters() as $param) {
            $paramClass = $param->getClass();
            if ($paramClass) { // #2.
                if ($paramClass->isInstance($request)) {
                    $arguments[] = $request;
                    continue;
                }
                $paramClassName = $paramClass->getName();
                if ($attribute = $request->getAttribute($paramClassName)) {
                    $arguments[] = $attribute;
                    continue;
                }
                if ($this->container->has($paramClassName)) {
                    try {
                        $arguments[] = $this->container->get($paramClassName);
                        continue;
                    } catch (NotFoundExceptionInterface $e) {
                    }
                }
            }
            if ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
                continue;
            }
            if ($attribute = $request->getAttribute($param->getName())) {
                $arguments[] = $attribute;
                continue;
            }
            throw new RuntimeException("not found parameter named \"{$param->getName()}\".");
        }
        return $arguments;
    }
}
