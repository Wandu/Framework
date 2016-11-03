<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\ValidatorNotFoundException;
use Wandu\Validator\Rules\ArrayValidator;

/**
 * @method \Wandu\Validator\Rules\PipelineValidator pipeline(array $validators = [])

 * @method \Wandu\Validator\Contracts\ValidatorInterface required()
 * @method \Wandu\Validator\Contracts\ValidatorInterface not(\Wandu\Validator\Contracts\ValidatorInterface $validator)
 * @method \Wandu\Validator\Contracts\ValidatorInterface array(array $attributes = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface collection($rule = null)
 * @method \Wandu\Validator\Contracts\ValidatorInterface iterable($rule = null)
 * @method \Wandu\Validator\Contracts\ValidatorInterface arrayable(array $attributes = [])
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
     * @return \Wandu\Validator\ValidatorFactory
     */
    public static function clearGlobal()
    {
        $clearedFactory = static::$factory;
        static::$factory = null;
        return $clearedFactory;
    }
    
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
     * @param string|array $namespaces
     * @return static
     */
    public function register($namespaces)
    {
        if (!is_array($namespaces)) {
            $namespaces = [$namespaces];
        }
        $this->namespaces = array_merge($this->namespaces, $namespaces);
        return $this;
    }

    /**
     * @param $rule
     * @return \Wandu\Validator\Contracts\ValidatorInterface
     */
    public function from($rule)
    {
        if ($rule instanceof ValidatorInterface) {
            return $rule;
        }
        if (is_array($rule)) {
            return new ArrayValidator($rule);
        }
        if (is_object($rule)) {
            return $this->object(get_object_vars($rule));
        }
        $rule = explode('|', $rule);
        if (count($rule) === 1) {
            return $this->createValidator($rule[0]);
        }
        // if count bigger than 1, need pipeline.
        $validators = [];
        foreach ($rule as $attribute) {
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
        preg_match_all('/\/[^\/]*\/|[^,]+/', substr($pattern, $pivot + 1), $matches);
        $params = array_reduce(
            $matches[0],
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
    private function underscoreToCamelCase($text)
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
