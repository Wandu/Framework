<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class ArrayValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'type.array';
    const ERROR_MESSAGE = 'it must be the array';
    
    const ATTRIBUTES_ERROR_TYPE = 'type.array.attributes';
    const ATTRIBUTES_ERROR_MESSAGE = 'it is array, but attributes are wrong';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface[] */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $name => $validator) {
            $this->attributes[$name] = ($validator instanceof ValidatorInterface)
                ? $validator
                : validator()->__call($validator);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item, $stopOnFail = false)
    {
        if (!is_array($item)) {
            throw $this->createException();
        }
        if ($stopOnFail) {
            return $this->assertStopOnFail();
        }
        $exceptions = [];
        foreach ($this->attributes as $name => $validator) {
            try {
                $validator->assert(isset($item[$name]) ? $item[$name] : null);
            } catch (InvalidValueException $e) {
                $exceptions[] = $e;
            }
        }
        if (count($exceptions)) {
            throw new InvalidValueException(
                static::ATTRIBUTES_ERROR_TYPE,
                static::ATTRIBUTES_ERROR_MESSAGE,
                $exceptions
            );
        }
    }
    
    public function assertStopOnFail()
    {
        try {
            foreach ($this->attributes as $name => $validator) {
                $validator->assert(isset($item[$name]) ? $item[$name] : null);
            }
        } catch (InvalidValueException $e) {
            throw new InvalidValueException(
                static::ATTRIBUTES_ERROR_TYPE,
                static::ATTRIBUTES_ERROR_MESSAGE
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if (!is_array($item)) {
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
