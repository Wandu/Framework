<?php
namespace Wandu\Validator\Rules;

class StringableValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'stringable';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_scalar($item);
    }
}
