<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

abstract class ValidatorAbstract implements ValidatorInterface
{
    const ERROR_TYPE = 'unknown';
    const ERROR_MESSAGE = 'something wrong';

    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        if (!$this->validate($item)) {
            throw $this->createException();
        }
    }

    /**
     * @return \Wandu\Validator\Exception\InvalidValueException
     */
    protected function createException()
    {
        return new InvalidValueException(static::ERROR_TYPE, static::ERROR_MESSAGE);
    }
}
