<?php
namespace Wandu\Validator\Rules;

class FloatableValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'floatable';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_numeric($item);
    }
}
