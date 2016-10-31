<?php
namespace Wandu\Validator\Rules;

class ExistsValidator extends RequiredValidator
{
    const ERROR_TYPE = 'exists';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return isset($item) && $item !== '';
    }
}
