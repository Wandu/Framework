<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\Validatable;

class ValidatorFactory
{
    /** @var \Wandu\Validator\ValidatorFactory */
    public static $instance;
    
    /** @var \Wandu\Validator\TesterLoader */
    protected $loader;
    
    /** @var \Wandu\Validator\RuleNormalizer */
    protected $normalizer;
    
    public function __construct(TesterLoader $loader = null, RuleNormalizer $normalizer = null)
    {
        $this->loader = $loader ?: new TesterLoader();
        $this->normalizer = $normalizer ?: new RuleNormalizer();
    }

    /**
     * @param string|\Wandu\Validator\Contracts\Rule $rule
     * @return \Wandu\Validator\Contracts\Validatable
     */
    public function factory($rule): Validatable
    {
        return new Validator($this->loader, $this->normalizer, $rule);
    }
}
