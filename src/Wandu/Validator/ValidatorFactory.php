<?php
namespace Wandu\Validator;

class ValidatorFactory
{
    /** @var \Wandu\Validator\TesterFactory */
    protected $tester;
    
    public function __construct(TesterFactory $tester = null)
    {
        if (!$tester) {
            $tester = new TesterFactory();
        }
        $this->tester = $tester;
    }

    /**
     * @param string|\Wandu\Validator\Contracts\Rule $rule
     * @return \Wandu\Validator\Validator
     */
    public function create($rule): Validator
    {
        return new Validator($this->tester, $rule);
    }
}
