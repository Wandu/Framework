<?php
namespace Wandu\Validator\Rules;

class IntegerValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'integer';
    const ERROR_MESSAGE = '{{name}} must be the integer';
    const ERROR_NOT_MESSAGE = '{{name}} must be not the integer';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_int($item);
    }
}
