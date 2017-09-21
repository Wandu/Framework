<?php
namespace Wandu\Sanitizer;

use Wandu\Sanitizer\Contracts\Rule;
use Wandu\Validator\Contracts\Validatable;

class Sanitizer
{
    /** @var \Wandu\Sanitizer\Contracts\Rule */
    protected $rule;
    
    /** @var \Wandu\Validator\Contracts\Validatable */
    protected $validator;
    
    public function __construct(Rule $rule, Validatable $validator)
    {
        $this->rule = $rule;
        $this->validator = $validator;
    }
    
    public function sanitize($data)
    {
        $this->validator->assert($data);
        return $this->rule->map($data);
    }
}
