<?php
namespace Wandu\Validator\Rules;

class IntegerValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'integer';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_int($item);
    }
}
