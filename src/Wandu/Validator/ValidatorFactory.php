<?php
namespace Wandu\Validator;

use Predis\Pipeline\Pipeline;
use Wandu\Validator\Rules\PipelineValidator;

/**
 * @method \Wandu\Validator\Contracts\ValidatorInterface optional(\Wandu\Validator\Contracts\ValidatorInterface $validator = null)
 * @method \Wandu\Validator\Contracts\ValidatorInterface array(array $attributes = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface integer()
 * @method \Wandu\Validator\Contracts\ValidatorInterface string()
 * @method \Wandu\Validator\Contracts\ValidatorInterface min(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface max(int $max)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMin(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMax(int $max)
 * @method \Wandu\Validator\Contracts\ValidatorInterface not(\Wandu\Validator\Contracts\ValidatorInterface $validator)
 */
class ValidatorFactory
{
    /** @var array */
    private $instances = [];

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
        if (!array_key_exists($name, $this->instances)) {
            $className = $this->getClassName($name);
            $this->instances[$name] = new $className();
        }
        return $this->instances[$name];
    }

    /**
     * @return \Wandu\Validator\Rules\PipelineValidator
     */
    public function pipeline()
    {
        return new PipelineValidator();
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
