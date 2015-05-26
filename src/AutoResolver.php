<?php
namespace Wandu\DI;

use Closure;
use ReflectionClass;

class AutoResolver
{
    protected $dependencies = [];

    protected $keys = [];

    protected $alias = [];

    public function singleton($class)
    {
        $this->keys[$class] = 'singleton';
        $this->dependencies[$class] = $this->analyzeConstructor($class);
    }

    protected function analyzeClass($class)
    {

    }

    protected function analyzeConstructor($class)
    {
        $refl = new Reflectionclass($class);
        $constructor = $refl->getConstructor();
        if (!isset($constructor)) {
            return [];
        }
        $dependencies = [];
        $params = $refl->getConstructor()->getParameters();
        foreach ($params as $param) {
            $type = null;
            if ($paramRefl = $param->getClass()) {
                $type = $paramRefl->getName();
            } elseif ($param->isArray()) {
                $type = 'array';
            } elseif ($param->isCallable()) {
                $type = 'callable';
            } else {
                $type = null;
            }
            $dependencies[] = $type;
        }
        return $dependencies;
    }
}
