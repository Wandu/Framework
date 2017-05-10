<?php
namespace Wandu\DI\Resolvers;

use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\ResolverInterface;
use Wandu\DI\Exception\CannotFindParameterException;

class BindResolver implements ResolverInterface
{
    /** @var string */
    protected $className;

    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ContainerInterface $container, array $arguments = [])
    {
        $reflClass = new ReflectionClass($this->className);
        $reflectionMethod = $reflClass->getConstructor();
        if (!$reflectionMethod) {
            $instance = $reflClass->newInstance();
        } else {
            $instance = $reflClass->newInstanceArgs(
                $this->getParameters($container, $reflectionMethod, $arguments)
            );
        }
        return $instance;
    }

    protected function getParameters(
        ContainerInterface $container,
        ReflectionFunctionAbstract $reflectionFunction,
        array $arguments = []
    ) {
        $parametersToReturn = static::getSeqArray($arguments);

        $reflectionParameters = array_slice($reflectionFunction->getParameters(), count($parametersToReturn));
        if (!count($reflectionParameters)) {
            return $parametersToReturn;
        }
        /* @var \ReflectionParameter $param */
        foreach ($reflectionParameters as $param) {
            /*
             * #1. search in arguments by parameter name
             * #1.1. search in arguments by class name
             * #2. if parameter has type hint
             * #2.1. search in container by class name
             * #3. if has default value, insert default value.
             * #4. exception
             */
            $paramName = $param->getName();
            try {
                if (array_key_exists($paramName, $arguments)) { // #1.
                    $parametersToReturn[] = $arguments[$paramName];
                    continue;
                }
                $paramClass = $param->getClass();
                if ($paramClass) { // #2.
                    $paramClassName = $paramClass->getName();
                    if ($container->has($paramClassName)) { // #2.1.
                        $parametersToReturn[] = $container->get($paramClassName);
                        continue;
                    }
                }
                if ($param->isDefaultValueAvailable()) { // #3.
                    $parametersToReturn[] = $param->getDefaultValue();
                    continue;
                }
                throw new CannotFindParameterException($paramName); // #4.
            } catch (ReflectionException $e) {
                throw new CannotFindParameterException($paramName);
            }
        }
        return $parametersToReturn;
    }
    
    /**
     * @param array $array
     * @return array
     */
    protected static function getSeqArray(array $array)
    {
        $arrayToReturn = [];
        foreach ($array as $key => $item) {
            if (is_int($key)) {
                $arrayToReturn[] = $item;
            }
        }
        return $arrayToReturn;
    }
}
