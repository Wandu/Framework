<?php
namespace Wandu\Validator;

/**
 * @return \Wandu\Validator\ValidatorFactory
 */
function validator()
{
    static $factory;
    if (!isset($factory)) {
        $factory = new ValidatorFactory();
    }
    return $factory;
}
