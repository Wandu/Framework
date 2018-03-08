<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Validatable;

/**
 * @param string|array|\Wandu\Validator\Contracts\Rule $rule
 * @return \Wandu\Validator\Contracts\Validatable
 */
function validator($rule): Validatable
{
    if (!isset(ValidatorFactory::$instance)) {
        ValidatorFactory::$instance = new ValidatorFactory();
    }
    return ValidatorFactory::$instance->factory($rule);
}
