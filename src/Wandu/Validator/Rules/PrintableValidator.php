<?php
namespace Wandu\Validator\Rules;

class PrintableValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'printable';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        if (is_object($item)) {
            return method_exists($item, '__toString');
        }
        return is_scalar($item);
    }
}
