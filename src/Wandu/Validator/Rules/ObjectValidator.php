<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class ObjectValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'object';

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
        /** @var \Wandu\Validator\Exception\InvalidValueException[] $exceptions */
        $exceptions = [];
        if (!$this->test($item)) {
            $exceptions[] = $this->createException();
        }
        foreach ($this->properties as $name => $validator) {
            try {
                $prefix = isset($this->name) ? "{$this->name}." : '';
                if ($validator instanceof ValidatorAbstract) {
                    $validator = $validator->withName($prefix . $name);
                }
                if (!is_object($item) || !object_get($item, $name)) {
                    throw new InvalidValueException('exists@' . $prefix . $name);
                }
                $validator->assert(object_get($item, $name));
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
        foreach ($this->properties as $name => $validator) {
            if (!is_object($item) || !object_get($item, $name)) {
                return false;
            }
            if (!$validator->validate(object_get($item, $name))) {
                return false;
            }
        }
        return true;
    }
}
