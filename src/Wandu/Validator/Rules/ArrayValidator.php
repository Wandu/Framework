<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class ArrayValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'array';
    const ERROR_MESSAGE = '{{name}} must be the array';
    
    const ATTRIBUTES_ERROR_TYPE = 'array.attributes';
    const ATTRIBUTES_ERROR_MESSAGE = '{{name}} is array, but attributes are wrong';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface[] */
    protected $attributes = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $name => $validator) {
            if (!($validator instanceof ValidatorInterface)) {
                $validator = validator()->__call($validator);
            }
            $this->attributes[$name] = $validator;
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
                if ($validator instanceof ValidatorAbstract) {
                    $prefix = isset($this->name) ? "{$this->name}." : '';
                    $validator = $validator->withName($prefix . $name);
                }
                $validator->assert(isset($item[$name]) ? $item[$name] : null);
            } catch (InvalidValueException $e) {
                $exceptions[] = $e;
            }
        }
        if (count($exceptions)) {
            $baseException = $exceptions[0];
            for ($i = 1, $length = count($exceptions); $i < $length; $i++) {
                $baseException->setMessages($exceptions[$i]->getMessages());
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
