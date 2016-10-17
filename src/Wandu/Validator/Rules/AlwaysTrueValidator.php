<?php
namespace Wandu\Validator\Rules;

/**
 * for test
 */
class AlwaysTrueValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'always_true';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return true;
    }
}
