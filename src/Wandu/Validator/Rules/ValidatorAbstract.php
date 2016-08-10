<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

abstract class ValidatorAbstract implements ValidatorInterface
{
    const ERROR_TYPE = 'unknown';
    const ERROR_MESSAGE = 'something wrong';
    const ERROR_NOT_MESSAGE = 'something wrong';

    /** @var string */
    protected $name;

    /**
     * @param string $name
     * @return static
     */
    public function withName($name)
    {
        $new = clone $this;
        $new->name = $name;
        return $new;
    }
    
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
        if (!$this->test($item)) {
            throw $this->createException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        return $this->test($item);
    }

    /**
     * @return \Wandu\Validator\Exception\InvalidValueException
     */
    protected function createException()
    {
        return new InvalidValueException($this->getErrorType(), $this->getErrorMessage());
    }

    /**
     * @return string
     */
    public function getErrorType()
    {
        return (isset($this->name) ? "{$this->name}:" : '') . static::ERROR_TYPE;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $message = str_replace(
            '{{name}}',
            isset($this->name) ? $this->name : 'it',
            static::ERROR_MESSAGE
        );
        foreach (get_object_vars($this) as $key => $value) {
            if (is_scalar($value)) {
                $message = str_replace('{{' . $key . '}}', $value, $message);
            }
        }
        return $message;
    }

    /**
     * @return string
     */
    public function getErrorNotMessage()
    {
        $message = str_replace(
            '{{name}}',
            isset($this->name) ? $this->name : 'it',
            static::ERROR_NOT_MESSAGE
        );
        foreach (get_object_vars($this) as $key => $value) {
            if (is_scalar($value)) {
                $message = str_replace('{{' . $key . '}}', $value, $message);
            }
        }
        return $message;
    }
}
