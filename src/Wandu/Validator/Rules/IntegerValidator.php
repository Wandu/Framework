<?php
namespace Wandu\Validator\Rules;

class IntegerValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'integer';
    const ERROR_MESSAGE = 'it must be the integer';

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        return is_int($item);
    }
}
