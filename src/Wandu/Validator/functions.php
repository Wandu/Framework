<?php
namespace Wandu\Validator;

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
