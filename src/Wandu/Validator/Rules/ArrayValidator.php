<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class ArrayValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'array';
    
    /** @var \Wandu\Validator\Contracts\ValidatorInterface[] */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $name => $validator) {
            $this->attributes[$name] = validator()->from($validator);
        }
    }

    /**
     * {@inheritdoc}
     */
    function test($item)
    {
        return is_array($item);
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        /** @var \Wandu\Validator\Exception\InvalidValueException[] $exceptions */
        $exceptions = [];
        if (!$this->test($item)) {
            $exceptions[] = $this->createException();
        }
        foreach ($this->attributes as $name => $validator) {
            try {
                $prefix = isset($this->name) ? "{$this->name}." : '';
                if ($validator instanceof ValidatorAbstract) {
                    $validator = $validator->withName($prefix . $name);
                }
                if (!is_array($item) || !array_key_exists($name, $item)) {
                    throw new InvalidValueException('exists@' . $prefix . $name);
                }
                $validator->assert($item[$name]);
            } catch (InvalidValueException $e) {
                $exceptions[] = $e;
            }
        }
        if (count($exceptions)) {
            $baseException = $exceptions[0];
            for ($i = 1, $length = count($exceptions); $i < $length; $i++) {
                $baseException->appendTypes($exceptions[$i]->getTypes());
            }
            throw $baseException;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if (!$this->test($item)) {
            return false;
        }
        foreach ($this->attributes as $name => $validator) {
            if (!$validator->validate(isset($item[$name]) ? $item[$name] : null)) {
                return false;
            }
        }
        return true;
    }
}
