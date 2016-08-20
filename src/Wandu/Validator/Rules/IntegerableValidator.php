<?php
namespace Wandu\Validator\Rules;

class IntegerableValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'integerable';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_numeric($item) && is_int($item + 0);
    }
}
