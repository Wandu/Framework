<?php
namespace Wandu\Validator;

/**
 * @return \Wandu\Validator\ValidatorFactory
 */
function validator()
{
    if (!isset(ValidatorFactory::$factory)) {
        (new ValidatorFactory)->setAsGlobal();
    }
    return ValidatorFactory::$factory;
}
