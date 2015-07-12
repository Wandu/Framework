<?php
namespace Wandu\DI\Reflection;

use Closure;
use Reflection;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionFunction;
use ReflectionObject;

class ReflectionCallable extends Reflection
{
    /** @var callable */
    private $callee;

    /**
     * @param callable $callee
     */
    public function __construct(callable $callee)
    {
        $this->callee = $callee;
    }

    /**
     * @return ReflectionFunctionAbstract
     */
    public function getMethod()
    {
        $callee = $this->callee;

        // closure, or function name,
        if ($callee instanceof Closure || (is_string($callee) && strpos($callee, '::') === false)) {
            return new ReflectionFunction($callee);
        }
        if (is_string($callee)) {
            $callee = explode('::', $callee);
        } elseif (is_object($callee)) {
            $callee = [$callee, '__invoke'];
        }
        if (is_object($callee[0])) {
            $reflectionObject = new ReflectionObject($callee[0]);
        } else {
            $reflectionObject = new ReflectionClass($callee[0]);
        }
        return $reflectionObject->getMethod($callee[1]);
    }
}
