<?php
namespace Wandu\Validator\Rules;

use Wandu\Validator\Contracts\ValidatorInterface;
use Wandu\Validator\Exception\InvalidValueException;

class AndValidator implements ValidatorInterface
{
    /** @var array|\Wandu\Validator\Contracts\ValidatorInterface[] */
    protected $validators;
    
    /**
     * @param \Wandu\Validator\Contracts\ValidatorInterface[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }
    
    /**
     * {@inheritdoc}
     */
    public function assert($item)
    {
        /** @var \Wandu\Validator\Exception\InvalidValueException[] $exceptions */
        $exceptions = [];
        foreach ($this->validators as $validator) {
            try {
                $validator->assert($item);
            } catch (InvalidValueException $exception) {
                $exceptions[] = $exception;
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
        foreach ($this->validators as $validator) {
            if (!$validator->validate($item)) {
                return false;
            }
        }
        return true;
    }
}
