<?php
namespace Wandu\Validator\Rules;

/**
 * for test
 */
class AlwaysFalseValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'always_false';

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return false;
    }
}
