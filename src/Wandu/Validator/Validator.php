<?php
namespace Wandu\Validator;

use Wandu\Validator\Contracts\RuleInterface;
use Wandu\Validator\Contracts\TesterInterface;
use Wandu\Validator\Exception\InvalidValueException;

class Validator
{
    /** @var string|\Wandu\Validator\Contracts\RuleInterface|\Wandu\Validator\Contracts\TesterInterface */
    protected $rule;
    
    /**
     * @param string|\Wandu\Validator\Contracts\RuleInterface|\Wandu\Validator\Contracts\TesterInterface $rule
     */
    public function __construct($rule)
    {
        $this->rule = $rule;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function assert($data)
    {
        if (is_string($this->rule)) {
            if (!tester($this->rule)->test($data)) {
                throw new InvalidValueException([$this->rule]);
            }
        }
        if ($this->rule instanceof TesterInterface) {
            if (!$this->rule->test($data)) {
                throw new InvalidValueException([get_class($this->rule)]);
            }
        }
        
        $errorBag = new ErrorBag();
        $this->rule->define(new AssertRuleDefinition($errorBag, $data));
        if (count($errorBag)) {
            throw new InvalidValueException($errorBag->errors());
        }
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool
    {
        try {
            $this->assert($data);
            return true;
        } catch (InvalidValueException $e) {}
        return false;
    }
}
