<?php
namespace Wandu\DI;

use Closure;
use ReflectionClass;

class AutoResolver extends Container
{
    /** @var array */
    protected $dependencies = [];

    /**
     * @param string $name it must be class or interface name
     * @param string $class it must be class name.
     */
    public function bind($name, $class = null)
    {
        if (!isset($class)) {
            $class = $name;
        }
        $this->keys[$name] = 'resolver';
        $this->dependencies[$name] = $class;
    }

    /**
     * @param string $class
     * @param string $method
     * @return object
     */
    public function resolve($class, $method = null)
    {
        return $this->offsetGet($class);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        if ($this->keys[$name] !== 'resolver') {
            return parent::offsetGet($name);
        }
        $class = $this->dependencies[$name];
        $refl = new ReflectionClass($class);
        $constructorRefl = $refl->getConstructor();
        $depends = [];
        if ($constructorRefl) {
            $params = $constructorRefl->getParameters();
            foreach ($params as $param) {
                if ($paramRefl = $param->getClass()) {
                    $depends[] = $this->offsetGet($paramRefl->getName());
                } else {
                    throw new CannotResolveException('Auto resolver can resolve the class that use params with type hint;' . $class);
                }
            }
        }
        return $refl->newInstanceArgs($depends);
    }
}
