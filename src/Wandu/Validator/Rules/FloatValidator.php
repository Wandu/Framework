<?php
namespace Wandu\Validator\Rules;

class FloatValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'float';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_float($item);
    }
}
