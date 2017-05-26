<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\RuleInterface;
use Wandu\Validator\Contracts\TesterInterface;

/**
 * @param string|\Wandu\Validator\Contracts\TesterInterface $tester
 * @return \Wandu\Validator\Contracts\TesterInterface
 */
function tester($tester): TesterInterface
{
    if (!isset(TesterFactory::$instance)) {
        (new TesterFactory)->setAsGlobal();
    }
    return TesterFactory::$instance->from($tester);
}

/**
 * @param string|\Wandu\Validator\Contracts\TesterInterface $validator
 * @return \Wandu\Validator\Contracts\TesterInterface
 */
function validator($validator)
{
    
}

/**
 * @param \Wandu\Validator\Contracts\RuleInterface $rule
 * @param string $model
 * @return \Wandu\Validator\Sanitizer
 */
function sanitizer(RuleInterface $rule, string $model): Sanitizer
{
    return new Sanitizer($rule, $model);
}
