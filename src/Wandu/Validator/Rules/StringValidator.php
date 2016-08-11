<?php
namespace Wandu\Validator\Rules;

class StringValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'string';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_string($item);
    }
}
