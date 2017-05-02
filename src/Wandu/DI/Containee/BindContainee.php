<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;
use ReflectionClass;

class BindContainee extends ContaineeAbstract
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
    protected function create(ContainerInterface $container)
    {
        $reflectionClass = new ReflectionClass($this->className);
        $reflectionMethod = $reflectionClass->getConstructor();
        if (!$reflectionMethod) {
            $instance = $reflectionClass->newInstance();
        } else {
            $instance = $reflectionClass->newInstanceArgs(
                $this->getParameters($container, $reflectionMethod)
            );
        }
        if ($this->annotatedEnabled) {
            $this->annotateAfterCreate($container, $instance);
        }
        return $instance;
    }
}
