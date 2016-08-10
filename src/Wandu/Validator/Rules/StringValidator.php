<?php
namespace Wandu\Validator\Rules;

class StringValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'string';
    const ERROR_MESSAGE = '{{name}} must be the string';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_string($item);
    }
}
