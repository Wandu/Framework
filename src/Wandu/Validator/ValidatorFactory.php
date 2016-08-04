<?php
namespace Wandu\Validator;

/**
 * @method \Wandu\Validator\Contracts\ValidatorInterface integer()
 * @method \Wandu\Validator\Contracts\ValidatorInterface array(array $attributes = [])
 */
class ValidatorFactory
{
    /** @var array */
    private static $instances = [];

    /**
     * @param string $name
     * @param array $arguments
     * @return \Wandu\Validator\Contracts\ValidatorInterface
     */
    public function __call($name, array $arguments = [])
    {
        if (count($arguments)) {
            $className = $this->getClassName($name);
            return new $className(...$arguments);
        }
        if (!array_key_exists($name, static::$instances)) {
            $className = $this->getClassName($name);
            static::$instances[$name] = new $className();
        }
        return static::$instances[$name];
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getClassName($name)
    {
        return __NAMESPACE__ . '\\Rules\\' . ucfirst($name) . 'Validator';
    }
}
