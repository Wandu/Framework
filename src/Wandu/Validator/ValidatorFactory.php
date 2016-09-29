<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\ValidatorNotFoundException;
use Wandu\Validator\Rules\ArrayValidator;

/**
 * @method \Wandu\Validator\Contracts\ValidatorInterface required()
 * @method \Wandu\Validator\Contracts\ValidatorInterface not(\Wandu\Validator\Contracts\ValidatorInterface $validator)
 * @method \Wandu\Validator\Contracts\ValidatorInterface array(array $attributes = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface object(array $properties = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface integer()
 * @method \Wandu\Validator\Contracts\ValidatorInterface boolean()
 * @method \Wandu\Validator\Contracts\ValidatorInterface float()
 * @method \Wandu\Validator\Contracts\ValidatorInterface string()
 * @method \Wandu\Validator\Contracts\ValidatorInterface integerable()
 * @method \Wandu\Validator\Contracts\ValidatorInterface floatable()
 * @method \Wandu\Validator\Contracts\ValidatorInterface numeric()
 * @method \Wandu\Validator\Contracts\ValidatorInterface stringable()
 * @method \Wandu\Validator\Contracts\ValidatorInterface printable()
 * @method \Wandu\Validator\Contracts\ValidatorInterface pipeline(array $validators = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface min(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface max(int $max)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMin(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMax(int $max)
 * @method \Wandu\Validator\Contracts\ValidatorInterface email(\Egulias\EmailValidator\Validation\EmailValidation $validation = null)
 * @method \Wandu\Validator\Contracts\ValidatorInterface regExp(string $pattern)
 */
class ValidatorFactory
{
    /** @var \Wandu\Validator\ValidatorFactory */
    public static $factory;
    
    /** @var array */
    private $instances = [];
    
    /** @var array */
    private $namespaces = [
        __NAMESPACE__ . '\\Rules',
    ];

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
     * @return \Wandu\Validator\ValidatorFactory
     */
    public function setAsGlobal()
    {
        $oldFactory = static::$factory;
        static::$factory = $this;
        return $oldFactory;
    }
    
    /**
     * @param $namespace
     * @return static
     */
    public function register($namespace)
    {
        $this->namespaces[] = $namespace;
        return $this;
    }

    /**
     * @param $attributes
     * @return \Wandu\Validator\Contracts\ValidatorInterface
     */
    public function from($attributes)
    {
        if ($attributes instanceof ValidatorInterface) {
            return $attributes;
        }
        if (is_array($attributes)) {
            return new ArrayValidator($attributes);
        }
        if (is_object($attributes)) {
            return $this->object(get_object_vars($attributes));
        }
        $attributes = explode('|', $attributes);
        if (count($attributes) === 1) {
            return $this->createValidator($attributes[0]);
        }
        // if count bigger than 1, need pipeline.
        $validators = [];
        foreach ($attributes as $attribute) {
            if ($validator = $this->createValidator($attribute)) {
                $validators[] = $validator;
            }
        }
        return validator()->pipeline($validators);
    }
    
    protected function createValidator($attribute)
    {
        $attribute = trim($attribute, ": \t\n\r\0\x0B");
        if (!$attribute) {
            return null;
        }
        list($method, $params) = $this->getMethodAndParams($attribute);
        $validator = $this->__call($this->underscoreToCamelCase($method), $params);
        if (substr($method, 0, 1) === '!') {
            $validator = $this->not($validator);
        }
        return $validator;
    }
    
    protected function getMethodAndParams($pattern)
    {
        if (false === $pivot = strpos($pattern, ':')) {
            return [$pattern, []]; // "simple"
        }
        $method = substr($pattern, 0, $pivot);
        $params = array_reduce(
            explode(',', substr($pattern, $pivot + 1)),
            function ($carry, $value) {
                $value = trim($value);
                if ($value) {
                    $carry[] = $value;
                }
                return $carry;
            },
            []
        );
        return [$method, $params];
    }

    /**
     * @param string $text
     * @return string
     */
    protected function underscoreToCamelCase($text)
    {
        $text = trim($text, '!');
        $text = str_replace(' ', '', ucwords(str_replace('_', ' ', $text)));
        $text[0] = strtolower($text[0]);
        return $text;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getClassName($name)
    {
        foreach (array_reverse($this->namespaces) as $baseNamespace) {
            $className = $baseNamespace . '\\' . ucfirst($name) . 'Validator';
            if (class_exists($className)) {
                return $className;
            }
        }
        throw new ValidatorNotFoundException($name);
    }
}
