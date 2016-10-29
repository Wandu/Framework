<?php
namespace Wandu\Validator\Rules;

use ArrayAccess;

class ArrayableValidator extends ArrayValidator
{
    const ERROR_TYPE = 'arrayable';

    /**
     * {@inheritdoc}
     */
    function test($item)
    {
        return is_array($item) || $item instanceof ArrayAccess;
    }
}
