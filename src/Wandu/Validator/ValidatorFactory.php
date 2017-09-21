<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Validatable;

class ValidatorFactory
{
    /** @var \Wandu\Validator\TesterLoader */
    protected $loader;
    
    public function __construct(TesterLoader $loader = null)
    {
        if (!$loader) {
            $loader = new TesterLoader();
        }
        $this->loader = $loader;
    }

    /**
     * @param string|\Wandu\Validator\Contracts\Rule $rule
     * @return \Wandu\Validator\Contracts\Validatable
     */
    public function factory($rule): Validatable
    {
        return new Validator($this->loader, $rule);
    }
}
