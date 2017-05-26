<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\RuleInterface;
use Wandu\Validator\Contracts\TesterInterface;

/**
 * @param string $tester
 * @param array ...$arguments
 * @return \Wandu\Validator\Contracts\TesterInterface
 */
function tester($tester, ...$arguments): TesterInterface
{
    if (!isset(TesterFactory::$instance)) {
        (new TesterFactory)->setAsGlobal();
    }
    return TesterFactory::$instance->from($tester, ...$arguments);
}

/**
 * @param string|\Wandu\Validator\Contracts\RuleInterface|\Wandu\Validator\Contracts\TesterInterface $rule
 * @return \Wandu\Validator\Validator
 */
function validator($rule): Validator
{
    return new Validator($rule);
}

/**
 * @param \Wandu\Validator\Contracts\RuleInterface $rule
 * @param string $T
 * @return \Wandu\Validator\Sanitizer
 */
function sanitizer(RuleInterface $rule, string $T = null): Sanitizer
{
    return new Sanitizer($rule, $T);
}
