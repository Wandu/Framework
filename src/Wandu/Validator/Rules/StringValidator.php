<?php
namespace Wandu\Validator\Rules;

class StringValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'type.string';
    const ERROR_MESSAGE = 'it must be the string';

    public function validate($item)
    {
        return is_string($item);
    }
}
