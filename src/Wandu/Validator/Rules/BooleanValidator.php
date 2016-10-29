<?php
namespace Wandu\Validator\Rules;

class BooleanValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'boolean';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_bool($item);
    }
}
