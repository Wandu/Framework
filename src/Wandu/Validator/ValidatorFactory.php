<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Rule;
use Wandu\Validator\Contracts\Validator;

class ValidatorFactory
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $tester;
    
    public function __construct(TesterLoader $tester = null)
    {
        if (!$tester) {
            $tester = new TesterLoader();
        }
        $this->tester = $tester;
    }

    /**
     * @param string|\Wandu\Validator\Contracts\Rule $rule
     * @return \Wandu\Validator\Contracts\Validator
     */
    public function factory($rule): Validator
    {
        if (is_string($rule)) {
            return new TesterValidator($rule, $this->tester->load($rule));
        }
        if ($rule instanceof Rule) {
            return new RuleValidator($this->tester, $rule);
        }
    }
}
