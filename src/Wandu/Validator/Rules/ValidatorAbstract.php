<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

abstract class ValidatorAbstract implements ValidatorInterface
{
    const ERROR_TYPE = 'unknown';

    /**
     * @param mixed $item
     * @return bool
     */
    abstract function test($item);
    
    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        if (!isset($item)) return;
        if ($item === '') return;
        if (!$this->test($item)) {
            throw $this->createException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if (!isset($item)) return true;
        return $this->test($item);
    }

    /**
     * @return \Wandu\Validator\Exception\InvalidValueException
     */
    protected function createException()
    {
        return new InvalidValueException($this->getErrorType());
    }

    /**
     * @return string
     */
    protected function getErrorType()
    {
        $type = static::ERROR_TYPE;
        foreach (get_object_vars($this) as $key => $value) {
            if (is_scalar($value)) {
                $type = str_replace('{{' . $key . '}}', $value, $type);
            }
        }
        return $type;
    }
}
