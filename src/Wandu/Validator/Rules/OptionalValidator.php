<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;
use function Wandu\Validator\validator;

/**
 * @method \Wandu\Validator\Rules\OptionalValidator optional(\Wandu\Validator\Contracts\ValidatorInterface $validator = null)
 * @method \Wandu\Validator\Contracts\ValidatorInterface array(array $attributes = [])
 * @method \Wandu\Validator\Contracts\ValidatorInterface integer()
 * @method \Wandu\Validator\Contracts\ValidatorInterface string()
 * @method \Wandu\Validator\Contracts\ValidatorInterface min(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface max(int $max)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMin(int $min)
 * @method \Wandu\Validator\Contracts\ValidatorInterface lengthMax(int $max)
 */
class OptionalValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'optional';
    const ERROR_MESSAGE = 'it must be null or empty string';

    /** @var \Wandu\Validator\Contracts\ValidatorInterface */
    protected $validator;

    /**
     * @param \Wandu\Validator\Contracts\ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return \Wandu\Validator\Contracts\ValidatorInterface
     */
    public function __call($name, array $arguments = [])
    {
        $this->validator =  validator()->__call($name, $arguments);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assert($item, $stopOnFail = false)
    {
        if ($item === null || $item === '') {
            return true;
        }
        $exception = $this->createException();
        if ($this->validator && !$stopOnFail) {
            try {
                $this->validator->validate($item);
                return; // break if success
            } catch (InvalidValueException $e) {
                $exception->insertException($e);                
            }
        }
        throw $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if ($item === null || $item === '') {
            return true;
        }
        if ($this->validator) {
            return $this->validator->validate($item);
        }
        return false;
    }
}
