<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

abstract class ValidatorAbstract implements ValidatorInterface
{
    const ERROR_TYPE = 'unknown';

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
        return new InvalidValueException($this->getErrorType());
    }

    /**
     * @return string
     */
    public function getErrorType()
    {
        $suffix = isset($this->name) ? '@' . $this->name : '';
        $type = static::ERROR_TYPE . $suffix;
        foreach (get_object_vars($this) as $key => $value) {
            if (is_scalar($value)) {
                $type = str_replace('{{' . $key . '}}', $value, $type);
            }
        }
        return $type;
    }
}
