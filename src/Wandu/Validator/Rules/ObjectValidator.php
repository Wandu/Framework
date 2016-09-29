<?php
namespace Wandu\Validator\Rules;

use stdClass;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class ObjectValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'object';
    const ERROR_PROPERTY_TYPE = 'object_property';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface[] */
    protected $properties = [];

    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $name => $validator) {
            $this->properties[$name] = validator()->from($validator);
        }
    }

    /**
     * {@inheritdoc}
     */
    function test($item)
    {
        return is_object($item);
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        if (!isset($item)) $item = new stdClass();
        /** @var \Wandu\Validator\Exception\InvalidValueException[] $exceptions */
        $exceptions = [];
        if (!$this->test($item)) {
            $exceptions['.'] = $this->createException();
        }
        foreach ($this->properties as $name => $validator) {
            try {
                $value = null;
                if (is_object($item)) {
                    $value = object_get($item, $name);
                }
                $validator->assert($value);
            } catch (InvalidValueException $e) {
                $exceptions[$name] = $e;
            }
        }
        if (count($exceptions)) {
            throw InvalidValueException::merge($exceptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if (!isset($item)) $item = new stdClass();
        if (!$this->test($item)) return false;

        foreach ($this->properties as $name => $validator) {
            if (!is_object($item) || object_get($item, $name) === null) {
                return false;
            }
            if (!$validator->validate(object_get($item, $name))) {
                return false;
            }
        }
        return true;
    }
}
