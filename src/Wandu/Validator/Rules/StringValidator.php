<?php
namespace Wandu\Validator\Rules;

class StringValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'string';
    const ERROR_MESSAGE = 'it must be the string';

    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        return is_string($item);
    }
}
