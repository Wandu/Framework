<?php
namespace Wandu\Validator\Rules;

use Traversable;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

class CollectionValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'collection';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface */
    protected $validator;

    /**
     * @param mixed $rule
     */
    public function __construct($rule = null)
    {
        if ($rule) {
            $this->validator = validator()->from($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    function test($items)
    {
        return is_array($items) || $items instanceof Traversable;
    }

    /**
     * {@inheritdoc}
     */
    public function assert($items)
    {
        if (!isset($items)) $items = [];
        /** @var \Wandu\Validator\Exception\InvalidValueException[] $exceptions */
        $exceptions = [];
        if (!$this->test($items)) {
            throw $this->createException();
        }
        if ($this->validator) {
            foreach ($items as $key => $item) {
                if (!is_int($key)) {
                    $exceptions['.'] = $this->createException();
                    continue;
                }
                try {
                    $this->validator->assert($item);
                } catch (InvalidValueException $e) {
                    $exceptions[$key] = $e;
                }
            }
        } else {
            foreach ($items as $key => $item) {
                if (!is_int($key)) {
                    $exceptions['.'] = $this->createException();
                }
            }
        }
        if (count($exceptions)) {
            throw InvalidValueException::merge($exceptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($items)
    {
        if (!isset($items)) $items = [];
        if (!$this->test($items)) return false;

        if ($this->validator) {
            foreach ($items as $key => $item) {
                if (!is_int($key)) return false;
                if (!$this->validator->validate($item)) return false;
            }
        } else {
            foreach ($items as $key => $item) {
                if (!is_int($key)) return false;
            }
        }
        return true;
    }
}
