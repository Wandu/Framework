<?php
namespace Wandu\Validator;

/**
 * @method \Wandu\Validator\Rules\OptionalValidator optional(\Wandu\Validator\Contracts\ValidatorInterface $validator = null)
 * @method \Wandu\Validator\Contracts\ValidatorInterface array(array $attributes = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface integer()
 * @method \Wandu\Validator\Contracts\ValidatorInterface string()
 * @method \Wandu\Validator\Contracts\ValidatorInterface min(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface max(int $max)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMin(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMax(int $max)
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
