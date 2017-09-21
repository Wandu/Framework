<?php
namespace Wandu\Sanitizer;

use Wandu\Sanitizer\Contracts\Rule;
use Wandu\Validator\TesterLoader;
use Wandu\Validator\ValidatorFactory;

class SanitizerFactory
{
    /** @var \Wandu\Validator\ValidatorFactory */
    protected $factory;
    
    public function __construct(TesterLoader $loader = null, SanitizerNormalizer $normalizer = null)
    {
        $this->factory = new ValidatorFactory(
            $loader ?: new TesterLoader(),
            $normalizer ?: new SanitizerNormalizer()
        );
    }

    /**
     * @param \Wandu\Sanitizer\Contracts\Rule $rule
     * @return \Wandu\Sanitizer\Sanitizer
     */
    public function factory(Rule $rule)
    {
        return new Sanitizer($rule, $this->factory->factory($rule->rule()));
    }
}
